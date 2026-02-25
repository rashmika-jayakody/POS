<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Branch;
use Illuminate\Http\Request;

class CashDrawerController extends Controller
{
    /**
     * Display the cash drawer / POS page with products for selection.
     */
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        $products = Product::with(['unit', 'productPrices', 'stocks'])->where('is_active', true)->orderBy('name')->get();
        $productsJson = $products->map(function ($p) {
            $prices = $p->productPrices->isNotEmpty()
                ? $p->productPrices->map(fn ($pp) => ['label' => $pp->label, 'price' => (float) $pp->price])->values()->all()
                : [['label' => 'Selling price', 'price' => (float) $p->selling_price]];
            $stock = (float) $p->stocks->sum('quantity');
            return [
                'id' => $p->id,
                'name' => $p->name,
                'code' => $p->code ?? '',
                'barcode' => $p->barcode ?? '',
                'category_id' => $p->category_id,
                'price' => (float) $p->selling_price,
                'prices' => $prices,
                'unit' => $p->unit ? $p->unit->short_code : 'unit',
                'stock' => $stock,
                'image' => $p->image_url ? asset($p->image_url) : null,
                'discount_type' => $p->discount_type,
                'discount_value' => (float) ($p->discount_value ?? 0),
            ];
        });
        $invoiceNo = 'INV-' . now()->format('Ymd') . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        $storeName = optional(auth()->user()->tenant)->name ?? config('app.name');
        $user = auth()->user();
        $inventoryBranch = $user->branch_id
            ? Branch::find($user->branch_id)
            : Branch::where('tenant_id', $user->tenant_id)->first();
        $inventoryBranchName = $inventoryBranch ? $inventoryBranch->name : null;
        return view('cash-drawer.index', compact('categories', 'productsJson', 'invoiceNo', 'storeName', 'inventoryBranchName'));
    }

    /**
     * Open the cash drawer.
     */
    public function open(Request $request)
    {
        // Logic to open cash drawer
        return response()->json(['success' => true, 'message' => 'Cash drawer opened']);
    }

    /**
     * Close the cash drawer.
     */
    public function close(Request $request)
    {
        // Logic to close cash drawer
        return response()->json(['success' => true, 'message' => 'Cash drawer closed']);
    }

    /**
     * Get cash drawer status.
     */
    public function status()
    {
        // Logic to get cash drawer status
        return response()->json(['status' => 'open']);
    }

    /**
     * Process return/refund and optionally update inventory (add returned qty back to stock).
     */
    public function processReturn(Request $request)
    {
        $validated = $request->validate([
            'invoice_no' => 'nullable|string|max:60',
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.qty' => 'required|numeric|min:0.001',
            'update_inventory' => 'boolean',
        ]);

        if (empty($validated['items'])) {
            return response()->json(['success' => true, 'message' => 'No items to return.']);
        }

        $inventoryUpdated = false;
        if (! empty($validated['update_inventory'])) {
            $user = $request->user();
            $branchId = $user->branch_id;
            if (! $branchId) {
                $branch = Branch::where('tenant_id', $user->tenant_id)->first();
                $branchId = $branch ? $branch->id : null;
            }
            if ($branchId) {
                foreach ($validated['items'] as $item) {
                    $stock = Stock::where('product_id', $item['product_id'])
                        ->where('branch_id', $branchId)
                        ->first();
                    if ($stock) {
                        $stock->increment('quantity', (float) $item['qty']);
                    }
                }
                $inventoryUpdated = true;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Return processed.',
            'inventory_updated' => $inventoryUpdated,
        ]);
    }
}
