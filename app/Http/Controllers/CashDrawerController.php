<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Branch;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\FifoStockService;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashDrawerController extends Controller
{
    public const POS_SHORTCUT_DEFAULTS = [
        'help' => 'F1',
        'search' => 'F2',
        'loyalty' => 'F3',
        'newBill' => 'F4',
        'hold' => 'F5',
        'refund' => 'F6',
        'pay' => 'F8',
        'clear' => 'F9',
        'newBill2' => 'Ctrl+N',
        'pay2' => 'Ctrl+P',
    ];

    /**
     * Display the cash drawer / POS page with products for selection.
     */
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        $products = Product::with(['unit', 'productPrices', 'stocks'])->where('is_active', true)->orderBy('name')->get();
        $productsJson = $products->map(function ($p) {
            $prices = [['label' => 'Selling price', 'price' => (float) $p->selling_price]];
            foreach ($p->productPrices as $pp) {
                if ($pp->label !== 'Selling price') {
                    $prices[] = ['label' => $pp->label, 'price' => (float) $pp->price];
                }
            }
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
        $settings = auth()->user()->tenant?->businessSetting;
        $storeName = $settings?->display_name ?? optional(auth()->user()->tenant)->name ?? config('app.name');
        $currencySymbol = $settings?->currency_symbol ?? 'Rs';
        $taxRate = $settings ? (float) $settings->tax_rate : 0;
        $taxLabel = $settings?->tax_label ?? 'Tax';
        $user = auth()->user();
        $inventoryBranch = $user->branch_id
            ? Branch::find($user->branch_id)
            : Branch::where('tenant_id', $user->tenant_id)->first();
        $inventoryBranchName = $inventoryBranch ? $inventoryBranch->name : null;
        $posShortcuts = $user->pos_shortcuts ?? self::POS_SHORTCUT_DEFAULTS;
        $posShortcuts = array_merge(self::POS_SHORTCUT_DEFAULTS, is_array($posShortcuts) ? $posShortcuts : []);

        return view('cash-drawer.index', compact('categories', 'productsJson', 'invoiceNo', 'storeName', 'currencySymbol', 'taxRate', 'taxLabel', 'inventoryBranchName', 'posShortcuts'));
    }

    /**
     * Update the logged-in user's POS keyboard shortcuts.
     */
    public function updateShortcuts(Request $request)
    {
        $validated = $request->validate([
            'shortcuts' => 'required|array',
            'shortcuts.help' => 'nullable|string|max:30',
            'shortcuts.search' => 'nullable|string|max:30',
            'shortcuts.loyalty' => 'nullable|string|max:30',
            'shortcuts.newBill' => 'nullable|string|max:30',
            'shortcuts.hold' => 'nullable|string|max:30',
            'shortcuts.refund' => 'nullable|string|max:30',
            'shortcuts.pay' => 'nullable|string|max:30',
            'shortcuts.clear' => 'nullable|string|max:30',
            'shortcuts.newBill2' => 'nullable|string|max:30',
            'shortcuts.pay2' => 'nullable|string|max:30',
        ]);

        $shortcuts = array_merge(self::POS_SHORTCUT_DEFAULTS, array_filter($validated['shortcuts']));
        $request->user()->update(['pos_shortcuts' => $shortcuts]);

        return response()->json(['success' => true, 'shortcuts' => $shortcuts]);
    }

    /**
     * Open the cash drawer.
     */
    public function open(Request $request)
    {
        ActivityLogService::log('cash_drawer_open', 'Cash drawer opened');
        return response()->json(['success' => true, 'message' => 'Cash drawer opened']);
    }

    /**
     * Close the cash drawer.
     */
    public function close(Request $request)
    {
        ActivityLogService::log('cash_drawer_close', 'Cash drawer closed');
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
     * Process sale and deduct stock from inventory.
     */
    public function processSale(Request $request)
    {
        $validated = $request->validate([
            'invoice_no' => 'required|string|max:60',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.qty' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'discount_total' => 'nullable|numeric|min:0',
            'tax_total' => 'nullable|numeric|min:0',
            'grand_total' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:40',
        ]);

        $user = $request->user();
        $tenantId = $user->tenant_id;
        $branchId = $user->branch_id;

        if (!$branchId) {
            $branch = Branch::where('tenant_id', $tenantId)->first();
            $branchId = $branch ? $branch->id : null;
        }

        if (!$tenantId || !$branchId) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid tenant or branch configuration.',
            ], 400);
        }

        $fifo = app(FifoStockService::class);
        $errors = [];

        // Validate stock availability before processing
        foreach ($validated['items'] as $item) {
            $productId = (int) $item['product_id'];
            $qty = (float) $item['qty'];
            
            // Check available stock
            $availableStock = $fifo->getAvailableQuantity($productId, $branchId);
            if ($availableStock < $qty) {
                $product = Product::find($productId);
                $errors[] = "Insufficient stock for {$product->name}. Available: {$availableStock}, Required: {$qty}";
            }
        }

        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'message' => 'Stock validation failed.',
                'errors' => $errors,
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Create sale record
            $sale = Sale::create([
                'tenant_id' => $tenantId,
                'branch_id' => $branchId,
                'user_id' => $user->id,
                'invoice_no' => $validated['invoice_no'],
                'sale_date' => now(),
                'subtotal' => $validated['subtotal'],
                'discount_total' => $validated['discount_total'] ?? 0,
                'tax_total' => $validated['tax_total'] ?? 0,
                'grand_total' => $validated['grand_total'],
                'payment_method' => $validated['payment_method'] ?? 'Cash',
            ]);

            // Process each item: deduct stock and create sale item
            // FIFO ensures oldest batches (lowest cost) are sold first
            foreach ($validated['items'] as $item) {
                $productId = (int) $item['product_id'];
                $qty = (float) $item['qty'];
                $unitPrice = (float) $item['unit_price'];
                $discountAmount = (float) ($item['discount_amount'] ?? 0);
                $lineTotal = ($qty * $unitPrice) - $discountAmount;

                // Deduct stock using FIFO and get total cost
                // Example: If selling 25 units and we have:
                // - Batch 1: 20 units @ 100rs (received first)
                // - Batch 2: 10 units @ 120rs (received later)
                // FIFO will: sell 20 from batch 1 (2,000rs) + 5 from batch 2 (600rs) = 2,600rs total cost
                $stockResult = $fifo->deduct($tenantId, $productId, $branchId, $qty);

                if (!$stockResult || !$stockResult['success']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Failed to deduct stock for product ID: {$productId}",
                    ], 400);
                }

                // Calculate average cost per unit for reporting
                // This represents the weighted average cost based on FIFO consumption
                // Total cost from FIFO / quantity sold = average cost per unit
                $totalCost = (float) $stockResult['cost'];
                $costPerUnit = $qty > 0 ? ($totalCost / $qty) : 0;

                // Create sale item with cost price
                // cost_price_at_sale stores the average cost per unit for COGS calculation
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $productId,
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                    'discount_amount' => $discountAmount,
                    'line_total' => $lineTotal,
                    'cost_price_at_sale' => $costPerUnit, // Average cost per unit (FIFO-based)
                ]);
            }

            DB::commit();

            // Log the sale
            ActivityLogService::log('sale_completed', "Sale completed: {$validated['invoice_no']}", [
                'sale_id' => $sale->id,
                'invoice_no' => $validated['invoice_no'],
                'grand_total' => $validated['grand_total'],
                'items_count' => count($validated['items']),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sale processed successfully.',
                'sale_id' => $sale->id,
                'invoice_no' => $sale->invoice_no,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error processing sale: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error processing sale: ' . $e->getMessage(),
            ], 500);
        }
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
            if ($branchId && $user->tenant_id) {
                $fifo = app(FifoStockService::class);
                foreach ($validated['items'] as $item) {
                    $qty = (float) $item['qty'];
                    if ($qty <= 0) {
                        continue;
                    }
                    $fifo->addBatch(
                        (int) $user->tenant_id,
                        (int) $item['product_id'],
                        (int) $branchId,
                        $qty,
                        'RET-' . now()->format('Ymd-His') . '-' . substr(uniqid(), -4),
                        null,
                        null
                    );
                }
                $inventoryUpdated = true;
            }
        }

        $invoiceNo = $validated['invoice_no'] ?? null;
        ActivityLogService::log('refund_processed', 'Refund processed' . ($invoiceNo ? " (Invoice: {$invoiceNo})" : ''), [
            'invoice_no' => $invoiceNo,
            'items_count' => count($validated['items']),
            'inventory_updated' => $inventoryUpdated,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Return processed.',
            'inventory_updated' => $inventoryUpdated,
        ]);
    }
}
