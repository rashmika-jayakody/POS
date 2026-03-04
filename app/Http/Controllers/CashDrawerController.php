<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Branch;
use App\Services\FifoStockService;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

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
