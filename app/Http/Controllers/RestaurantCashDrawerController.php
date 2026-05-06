<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\RestaurantOrder;
use App\Models\RestaurantOrderItem;
use App\Models\RestaurantTable;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\CashDrawerSessionService;
use App\Services\FifoStockService;
use App\Services\PrintService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RestaurantCashDrawerController extends Controller
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

    public function index()
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id;
        $branchId = $user->branch_id;

        if (! $branchId) {
            $branch = Branch::where('tenant_id', $tenantId)->first();
            $branchId = $branch?->id ?? null;
        }

        $categories = Category::orderBy('name')->get();
        $products = Product::with(['unit', 'productPrices', 'stocks'])->where('is_active', true)->orderBy('name')->get();
        $productsJson = $products->map(function ($p) {
            $prices = [['label' => 'Selling price', 'price' => (float) $p->selling_price]];
            foreach ($p->productPrices as $pp) {
                if ($pp->label !== 'Selling price') {
                    $prices[] = ['label' => $pp->label, 'price' => (float) $pp->price];
                }
            }

            return [
                'id' => $p->id,
                'name' => $p->name,
                'code' => $p->code ?? '',
                'barcode' => $p->barcode ?? '',
                'category_id' => $p->category_id,
                'price' => (float) $p->selling_price,
                'prices' => $prices,
                'unit' => $p->unit ? $p->unit->short_code : 'unit',
                'stock' => 999999,
                'image' => $p->image_url ? asset($p->image_url) : null,
                'discount_type' => $p->discount_type,
                'discount_value' => (float) ($p->discount_value ?? 0),
                'has_modifiers' => $p->has_modifiers,
                'modifiers' => $p->modifiers ? $p->modifiers->map(function ($m) {
                    return [
                        'id' => $m->id,
                        'name' => $m->name,
                        'type' => $m->type,
                        'price_adjustment' => (float) $m->price_adjustment,
                        'is_required' => $m->is_required,
                    ];
                })->toArray() : [],
            ];
        });

        $invoiceNo = 'ORD-'.now()->format('Ymd').'-'.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        $settings = $user->tenant?->businessSetting;
        $storeName = $settings?->display_name ?? optional($user->tenant)->name ?? config('app.name');
        $currencySymbol = $settings?->currency_symbol ?? 'Rs';
        $taxRate = $settings ? (float) $settings->tax_rate : 0;
        $taxLabel = $settings?->tax_label ?? 'Tax';
        $serviceChargeRate = $settings ? (float) ($settings->service_charge_rate ?? 0) : 100;
        $inventoryBranch = $user->branch_id
            ? Branch::find($user->branch_id)
            : Branch::where('tenant_id', $user->tenant_id)->first();
        $inventoryBranchName = $inventoryBranch ? $inventoryBranch->name : null;
        $posShortcuts = $user->pos_shortcuts ?? self::POS_SHORTCUT_DEFAULTS;
        $posShortcuts = array_merge(self::POS_SHORTCUT_DEFAULTS, is_array($posShortcuts) ? $posShortcuts : []);

        $tables = RestaurantTable::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('floor_section')
            ->orderBy('name')
            ->get();

        $waiters = User::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $sessionService = app(CashDrawerSessionService::class);
        $activeSession = $sessionService->getActiveSession($tenantId, $branchId);

        $unpaidOrders = RestaurantOrder::where('tenant_id', $tenantId)
            ->whereIn('status', ['confirmed', 'preparing', 'ready', 'served'])
            ->where('is_paid', false)
            ->with(['table', 'user', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('restaurant-cash-drawer.index', compact(
            'categories',
            'productsJson',
            'invoiceNo',
            'storeName',
            'currencySymbol',
            'taxRate',
            'taxLabel',
            'serviceChargeRate',
            'inventoryBranchName',
            'posShortcuts',
            'tables',
            'waiters',
            'unpaidOrders',
            'activeSession'
        ));
    }

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

    public function open(Request $request)
    {
        ActivityLogService::log('cash_drawer_open', 'Cash drawer opened');

        return response()->json(['success' => true, 'message' => 'Cash drawer opened']);
    }

    public function close(Request $request)
    {
        ActivityLogService::log('cash_drawer_close', 'Cash drawer closed');

        return response()->json(['success' => true, 'message' => 'Cash drawer closed']);
    }

    public function status()
    {
        return response()->json(['status' => 'open']);
    }

    public function getSessionStatus(Request $request)
    {
        $user = $request->user();
        $sessionService = app(CashDrawerSessionService::class);
        $session = $sessionService->getActiveSession($user->tenant_id, $user->branch_id);

        if (! $session) {
            return response()->json([
                'is_open' => false,
                'session' => null,
            ]);
        }

        $totals = $sessionService->calculateSessionTotals($session);
        $expectedBalance = $sessionService->calculateExpectedBalance($session, $totals);

        return response()->json([
            'is_open' => true,
            'session' => $session,
            'totals' => $totals,
            'expected_balance' => $expectedBalance,
        ]);
    }

    public function openSession(Request $request)
    {
        $validated = $request->validate([
            'opening_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = $request->user();
        $sessionService = app(CashDrawerSessionService::class);

        try {
            $session = $sessionService->openSession(
                $user->tenant_id,
                $user->branch_id ?? Branch::where('tenant_id', $user->tenant_id)->first()->id,
                $user->id,
                (float) $validated['opening_balance'],
                $validated['notes'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Cash drawer session opened successfully.',
                'session' => $session,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function closeSession(Request $request)
    {
        $validated = $request->validate([
            'closing_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = $request->user();
        $sessionService = app(CashDrawerSessionService::class);
        $session = $sessionService->getActiveSession($user->tenant_id, $user->branch_id);

        if (! $session) {
            return response()->json([
                'success' => false,
                'message' => 'No active cash drawer session found.',
            ], 400);
        }

        try {
            $session = $sessionService->closeSession(
                $session,
                (float) $validated['closing_balance'],
                $validated['notes'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Cash drawer session closed successfully.',
                'session' => $session,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

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
                        'RET-'.now()->format('Ymd-His').'-'.substr(uniqid(), -4),
                        null,
                        null
                    );
                }
                $inventoryUpdated = true;
            }
        }

        $invoiceNo = $validated['invoice_no'] ?? null;
        ActivityLogService::log('refund_processed', 'Refund processed'.($invoiceNo ? " (Invoice: {$invoiceNo})" : ''), [
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

    public function createOrder(Request $request)
    {
        $validated = $request->validate([
            'order_no' => 'required|string|max:60',
            'restaurant_table_id' => 'nullable|exists:restaurant_tables,id',
            'user_id' => 'nullable|exists:users,id',
            'customer_name' => 'nullable|string|max:200',
            'customer_phone' => 'nullable|string|max:20',
            'order_type' => 'required|in:dine_in,takeout,delivery',
            'guest_count' => 'nullable|integer|min:1',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.special_instructions' => 'nullable|string',
            'items.*.modifiers' => 'nullable|array',
            'subtotal' => 'required|numeric|min:0',
            'discount_total' => 'nullable|numeric|min:0',
            'tax_total' => 'required|numeric|min:0',
            'service_charge' => 'required|numeric|min:0',
            'grand_total' => 'required|numeric|min:0',
            'tip_amount' => 'nullable|numeric|min:0',
            'tip_type' => 'nullable|in:fixed,percentage',
            'payment_method' => 'nullable|string|max:40',
            'is_paid' => 'nullable|boolean',
            'send_to_kitchen' => 'nullable|boolean',
        ]);

        $user = $request->user();
        $branchId = $user->branch_id ?? Branch::where('tenant_id', $user->tenant_id)->first()->id;

        $sessionService = app(CashDrawerSessionService::class);
        $activeSession = $sessionService->getActiveSession($user->tenant_id, $branchId);

        $isPaid = $validated['is_paid'] ?? false;
        $sendToKitchen = $validated['send_to_kitchen'] ?? false;

        if ($isPaid && ! $validated['payment_method']) {
            return response()->json([
                'success' => false,
                'message' => 'Payment method is required when processing payment.',
            ], 400);
        }

        $status = $isPaid ? 'completed' : ($sendToKitchen ? 'confirmed' : 'pending');

        $orderData = [
            'tenant_id' => $user->tenant_id,
            'branch_id' => $branchId,
            'restaurant_table_id' => $validated['restaurant_table_id'] ?? null,
            'user_id' => $validated['user_id'] ?? $user->id,
            'customer_name' => $validated['customer_name'] ?? null,
            'customer_phone' => $validated['customer_phone'] ?? null,
            'order_no' => $validated['order_no'],
            'order_type' => $validated['order_type'],
            'status' => $status,
            'guest_count' => $validated['guest_count'] ?? null,
            'subtotal' => $validated['subtotal'],
            'discount_total' => $validated['discount_total'] ?? 0,
            'tax_total' => $validated['tax_total'],
            'service_charge' => $validated['service_charge'],
            'grand_total' => $validated['grand_total'],
            'is_paid' => $isPaid,
            'payment_method' => $validated['payment_method'] ?? null,
            'tip_amount' => $validated['tip_amount'] ?? 0,
            'tip_type' => $validated['tip_type'] ?? null,
            'cash_drawer_session_id' => $activeSession?->id,
        ];

        if ($isPaid) {
            $orderData['paid_at'] = now();
            $orderData['completed_at'] = now();
        } elseif ($sendToKitchen || $status === 'confirmed') {
            $orderData['confirmed_at'] = now();
        }

        $order = RestaurantOrder::create($orderData);

        $orderItems = [];
        foreach ($validated['items'] as $item) {
            $lineTotal = (float) $item['qty'] * (float) $item['unit_price'];
            $modifierTotal = 0;
            if (! empty($item['modifiers'])) {
                foreach ($item['modifiers'] as $mod) {
                    $modifierTotal += (float) ($mod['price_adjustment'] ?? 0);
                }
            }
            $orderItems[] = [
                'restaurant_order_id' => $order->id,
                'product_id' => $item['product_id'],
                'qty' => $item['qty'],
                'unit_price' => $item['unit_price'],
                'line_total' => $lineTotal,
                'modifier_total' => $modifierTotal,
                'discount_amount' => 0,
                'special_instructions' => $item['special_instructions'] ?? null,
                'status' => $isPaid ? 'served' : 'pending',
            ];
        }

        foreach ($orderItems as $orderItem) {
            RestaurantOrderItem::create($orderItem);
        }

        if ($isPaid) {
            $this->reduceStockForOrder($order);
            $this->updateSessionTotals($activeSession, $validated['grand_total'], $validated['payment_method'] ?? 'Cash', $validated['tip_amount'] ?? 0);
        }

        if ($order->restaurant_table_id) {
            $table = RestaurantTable::find($order->restaurant_table_id);
            if ($table) {
                $table->update(['status' => $isPaid ? 'available' : 'occupied']);
            }
        }

        ActivityLogService::log('order_created', "Order {$order->order_no} created", [
            'order_id' => $order->id,
            'order_no' => $order->order_no,
            'grand_total' => $order->grand_total,
            'is_paid' => $isPaid,
        ]);

        return response()->json([
            'success' => true,
            'message' => $isPaid ? 'Payment processed successfully' : 'Order sent to kitchen',
            'order_id' => $order->id,
            'order_no' => $order->order_no,
            'is_paid' => $isPaid,
        ]);
    }

    public function payOrder(Request $request, $orderNo)
    {
        $validated = $request->validate([
            'payment_method' => 'required|string|max:40',
            'tip_amount' => 'nullable|numeric|min:0',
            'tip_type' => 'nullable|in:fixed,percentage',
        ]);

        $user = $request->user();
        $order = RestaurantOrder::where('order_no', $orderNo)
            ->where('tenant_id', $user->tenant_id)
            ->first();

        if (! $order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        if ($order->is_paid) {
            return response()->json(['success' => false, 'message' => 'Order already paid'], 400);
        }

        $branchId = $order->branch_id;
        $sessionService = app(CashDrawerSessionService::class);
        $activeSession = $sessionService->getActiveSession($user->tenant_id, $branchId);

        $tipAmount = $validated['tip_amount'] ?? 0;
        $order->markAsPaid($validated['payment_method'], $tipAmount, $validated['tip_type'] ?? null);

        if ($activeSession) {
            $order->update(['cash_drawer_session_id' => $activeSession->id]);
        }

        $this->reduceStockForOrder($order);

        $this->updateSessionTotals($activeSession, $order->grand_total, $validated['payment_method'], $tipAmount);

        if ($order->restaurant_table_id) {
            $table = RestaurantTable::find($order->restaurant_table_id);
            if ($table) {
                $table->update(['status' => 'available']);
            }
        }

        ActivityLogService::log('order_paid', "Order {$order->order_no} paid", [
            'order_id' => $order->id,
            'order_no' => $order->order_no,
            'payment_method' => $validated['payment_method'],
            'grand_total' => $order->grand_total,
            'tip_amount' => $tipAmount,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment processed successfully',
            'order_id' => $order->id,
        ]);
    }

    public function printReceipt(Request $request, $orderNo)
    {
        $user = $request->user();
        $order = RestaurantOrder::where('order_no', $orderNo)
            ->where('tenant_id', $user->tenant_id)
            ->with(['items.product', 'table'])
            ->first();

        if (! $order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        $printService = app(PrintService::class);

        $data = [
            'order_no' => $order->order_no,
            'customer_name' => $order->customer_name,
            'items' => $order->items->map(function ($item) {
                return [
                    'name' => $item->product->name ?? 'Unknown',
                    'qty' => $item->qty,
                    'unit_price' => $item->unit_price,
                ];
            })->toArray(),
            'subtotal' => $order->subtotal,
            'discount_total' => $order->discount_total,
            'tax_total' => $order->tax_total,
            'service_charge' => $order->service_charge,
            'grand_total' => $order->grand_total,
            'tip_amount' => $order->tip_amount,
            'payment_method' => $order->payment_method,
            'table' => $order->table?->name,
        ];

        $html = $printService->generateReceiptHtml($data);

        return response()->json([
            'success' => true,
            'html' => $html,
        ]);
    }

    public function printKitchenTicket(Request $request, $orderNo)
    {
        $user = $request->user();
        $order = RestaurantOrder::where('order_no', $orderNo)
            ->where('tenant_id', $user->tenant_id)
            ->with(['items.product', 'table', 'user'])
            ->first();

        if (! $order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        $printService = app(PrintService::class);

        $data = [
            'order_no' => $order->order_no,
            'table' => $order->table?->name,
            'waiter' => $order->user?->name,
            'items' => $order->items->map(function ($item) {
                return [
                    'name' => $item->product->name ?? 'Unknown',
                    'qty' => $item->qty,
                    'special_instructions' => $item->special_instructions,
                    'modifiers' => [],
                ];
            })->toArray(),
        ];

        $success = $printService->printKitchenTicket($data);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Kitchen ticket printed' : 'Failed to print kitchen ticket',
        ]);
    }

    protected function reduceStockForOrder(RestaurantOrder $order)
    {
        $tenantId = $order->tenant_id;
        $branchId = $order->branch_id;

        if (! $tenantId || ! $branchId) {
            return;
        }

        $fifo = app(FifoStockService::class);
        $order->load('items');

        try {
            DB::beginTransaction();

            foreach ($order->items as $item) {
                $productId = (int) $item->product_id;
                $qty = (float) $item->qty;

                if ($qty <= 0) {
                    continue;
                }

                $stockResult = $fifo->deduct($tenantId, $productId, $branchId, $qty);

                if (! $stockResult || ! $stockResult['success']) {
                    \Log::warning('Failed to deduct stock for restaurant order item', [
                        'order_id' => $order->id,
                        'product_id' => $productId,
                        'qty' => $qty,
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error reducing stock for restaurant order: '.$e->getMessage(), [
                'order_id' => $order->id,
            ]);
        }
    }

    protected function updateSessionTotals($session, $grandTotal, $paymentMethod, $tipAmount = 0)
    {
        if (! $session) {
            return;
        }

        $session->refresh();

        $paymentMethod = strtolower($paymentMethod ?? 'cash');

        if ($paymentMethod === 'cash') {
            $session->increment('cash_sales', $grandTotal);
        } elseif ($paymentMethod === 'card') {
            $session->increment('card_sales', $grandTotal);
        } else {
            $session->increment('other_sales', $grandTotal);
        }

        if ($tipAmount > 0) {
            $session->increment('other_sales', $tipAmount);
        }
    }
}
