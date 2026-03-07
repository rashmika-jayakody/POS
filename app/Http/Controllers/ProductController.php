<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Branch;
use App\Models\Stock;
use App\Services\ActivityLogService;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['category', 'unit', 'stocks.branch'])->get();
        $posType = auth()->user()->tenant?->pos_type ?? 'retail';
        $settings = auth()->user()->tenant?->businessSetting;
        $currencySymbol = $settings?->currency_symbol ?? 'Rs';
        return view('products.index', compact('products', 'posType', 'currencySymbol'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $units = Unit::all();
        $settings = auth()->user()->tenant?->businessSetting;
        $currencySymbol = $settings?->currency_symbol ?? 'Rs';
        return view('products.create', compact('categories', 'units', 'currencySymbol'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'code' => 'nullable|string|max:60',
            'barcode' => 'nullable|string|unique:products,barcode',
            'cost_price' => 'nullable|numeric|min:0', // Read-only, set from GRN
            'selling_price' => 'required|numeric|min:0',
            'discount_type' => 'nullable|string|in:flat,percent',
            'discount_value' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        
        // Set default cost_price to 0 if not provided (will be updated when GRN is received)
        $validated['cost_price'] = $validated['cost_price'] ?? 0;
        $validated['discount_type'] = $validated['discount_type'] ?? null;
        $validated['discount_value'] = isset($validated['discount_value']) ? (float) $validated['discount_value'] : 0;

        $product = Product::create($validated);

        $hasPrices = false;
        foreach ($request->input('prices', []) as $p) {
            if (! empty($p['label']) && isset($p['price']) && is_numeric($p['price']) && (float) $p['price'] >= 0) {
                ProductPrice::create([
                    'product_id' => $product->id,
                    'label' => $p['label'],
                    'price' => (float) $p['price'],
                ]);
                $hasPrices = true;
            }
        }
        if (! $hasPrices) {
            ProductPrice::create([
                'product_id' => $product->id,
                'label' => 'Selling price',
                'price' => (float) $product->selling_price,
            ]);
        }

        // Only create stock records for retail POS (restaurants don't track menu item stock)
        $posType = auth()->user()->tenant?->pos_type ?? 'retail';
        if ($posType === 'retail') {
            $branches = Branch::all();
            foreach ($branches as $branch) {
                Stock::create([
                    'product_id' => $product->id,
                    'branch_id' => $branch->id,
                    'quantity' => 0,
                    'low_stock_threshold' => 10,
                ]);
            }
        }

        ActivityLogService::log('product_created', "Product created: {$product->name}", ['product_id' => $product->id, 'code' => $product->code], Product::class, $product->id);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with(['category', 'unit', 'stocks.branch'])->findOrFail($id);
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::with('productPrices')->findOrFail($id);
        $categories = Category::all();
        $units = Unit::all();
        $posType = auth()->user()->tenant?->pos_type ?? 'retail';
        
        // Get latest purchase price from GRN batches (FIFO)
        $latestBatch = \App\Models\StockBatch::where('product_id', $product->id)
            ->where('tenant_id', $product->tenant_id)
            ->where('quantity', '>', 0)
            ->orderBy('received_at', 'desc')
            ->orderBy('id', 'desc')
            ->first();
        
        $latestPurchasePrice = $latestBatch ? (float) $latestBatch->purchase_price : (float) ($product->cost_price ?? 0);
        
        $settings = auth()->user()->tenant?->businessSetting;
        $currencySymbol = $settings?->currency_symbol ?? 'Rs';
        
        return view('products.edit', compact('product', 'categories', 'units', 'posType', 'latestPurchasePrice', 'currencySymbol'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'code' => 'nullable|string|max:60',
            'barcode' => 'nullable|string|unique:products,barcode,' . $id,
            'cost_price' => 'nullable|numeric|min:0', // Read-only, set from GRN
            'selling_price' => 'required|numeric|min:0',
            'discount_type' => 'nullable|string|in:flat,percent',
            'discount_value' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        
        // Use latest purchase price from GRN if available, otherwise keep existing
        if (!isset($validated['cost_price']) || $validated['cost_price'] == 0) {
            $latestBatch = \App\Models\StockBatch::where('product_id', $product->id)
                ->where('tenant_id', $product->tenant_id)
                ->where('quantity', '>', 0)
                ->orderBy('received_at', 'desc')
                ->orderBy('id', 'desc')
                ->first();
            
            $validated['cost_price'] = $latestBatch ? (float) $latestBatch->purchase_price : $product->cost_price;
        }
        $validated['discount_type'] = $validated['discount_type'] ?? null;
        $validated['discount_value'] = isset($validated['discount_value']) ? (float) $validated['discount_value'] : 0;

        $product->update($validated);

        $product->productPrices()->delete();
        $hasPrices = false;
        foreach ($request->input('prices', []) as $p) {
            if (! empty($p['label']) && isset($p['price']) && is_numeric($p['price']) && (float) $p['price'] >= 0) {
                ProductPrice::create([
                    'product_id' => $product->id,
                    'label' => $p['label'],
                    'price' => (float) $p['price'],
                ]);
                $hasPrices = true;
            }
        }
        if (! $hasPrices) {
            ProductPrice::create([
                'product_id' => $product->id,
                'label' => 'Selling price',
                'price' => (float) $product->selling_price,
            ]);
        }

        ActivityLogService::log('product_updated', "Product updated: {$product->name}", ['product_id' => $product->id], Product::class, $product->id);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $name = $product->name;
        $product->productPrices()->delete();
        $product->stocks()->delete();
        $product->delete();

        ActivityLogService::log('product_deleted', "Product deleted: {$name}", ['product_id' => (int) $id]);

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
