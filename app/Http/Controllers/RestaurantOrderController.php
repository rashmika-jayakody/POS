<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\RestaurantOrder;
use App\Models\RestaurantOrderItem;
use App\Models\RestaurantTable;
use App\Services\ActivityLogService;
use App\Services\CashDrawerSessionService;
use App\Services\FifoStockService;
use App\Services\PrintService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RestaurantOrderController extends Controller
{
    public function index(): View
    {
        $orders = RestaurantOrder::where('tenant_id', auth()->user()->tenant_id)
            ->with(['table', 'user', 'customer', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $settings = auth()->user()->tenant?->businessSetting;
        $currencySymbol = $settings?->currency_symbol ?? 'Rs';

        return view('restaurant.orders.index', compact('orders', 'currencySymbol'));
    }

    public function create()
    {
        return view('restaurant.orders.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_no' => 'required|string|max:60|unique:restaurant_orders,order_no',
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
            'subtotal' => 'required|numeric|min:0',
            'tax_total' => 'required|numeric|min:0',
            'service_charge' => 'required|numeric|min:0',
            'grand_total' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:40',
            'is_paid' => 'nullable|boolean',
            'tip_amount' => 'nullable|numeric|min:0',
            'tip_type' => 'nullable|in:fixed,percentage',
        ]);

        $user = auth()->user();
        $tenantId = $user->tenant_id;
        $branchId = $user->branch_id;

        if (! $branchId) {
            $branch = Branch::where('tenant_id', $tenantId)->first();
            $branchId = $branch?->id;
        }

        if (! $branchId) {
            return response()->json([
                'success' => false,
                'message' => 'No branch assigned to user.',
            ], 400);
        }

        $activeSession = app(CashDrawerSessionService::class)->getActiveSession($tenantId, $branchId);

        $isPaid = $validated['is_paid'] ?? false;
        $status = $isPaid ? 'completed' : 'confirmed';

        $orderData = [
            'tenant_id' => $tenantId,
            'branch_id' => $branchId,
            'restaurant_table_id' => $validated['restaurant_table_id'] ?? null,
            'user_id' => $validated['user_id'] ?? null,
            'customer_name' => $validated['customer_name'] ?? null,
            'customer_phone' => $validated['customer_phone'] ?? null,
            'order_no' => $validated['order_no'],
            'order_type' => $validated['order_type'],
            'status' => $status,
            'guest_count' => $validated['guest_count'] ?? null,
            'subtotal' => $validated['subtotal'],
            'tax_total' => $validated['tax_total'],
            'service_charge' => $validated['service_charge'],
            'grand_total' => $validated['grand_total'],
            'is_paid' => $isPaid,
            'payment_method' => $validated['payment_method'] ?? null,
            'tip_amount' => $validated['tip_amount'] ?? 0,
            'tip_type' => $validated['tip_type'] ?? null,
            'cash_drawer_session_id' => $activeSession?->id,
        ];

        if ($status === 'confirmed') {
            $orderData['confirmed_at'] = now();
        } else {
            $orderData['completed_at'] = now();
            $orderData['paid_at'] = now();
        }

        $order = RestaurantOrder::create($orderData);

        foreach ($validated['items'] as $item) {
            $lineTotal = (float) $item['qty'] * (float) $item['unit_price'];

            RestaurantOrderItem::create([
                'restaurant_order_id' => $order->id,
                'product_id' => $item['product_id'],
                'qty' => $item['qty'],
                'unit_price' => $item['unit_price'],
                'line_total' => $lineTotal,
                'modifier_total' => 0,
                'discount_amount' => 0,
                'special_instructions' => $item['special_instructions'] ?? null,
                'status' => $isPaid ? 'served' : 'pending',
            ]);
        }

        if ($isPaid) {
            $this->reduceStockForOrder($order);
            if ($activeSession) {
                $activeSession->increment('other_sales', $order->grand_total);
            }
        }

        if ($order->restaurant_table_id) {
            $table = RestaurantTable::find($order->restaurant_table_id);
            if ($table) {
                $table->update(['status' => 'occupied']);
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
            'message' => 'Order saved successfully',
            'order_id' => $order->id,
            'order_no' => $order->order_no,
        ]);
    }

    public function show(RestaurantOrder $order)
    {
        if ($order->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $order->load(['table', 'user', 'customer', 'items.product', 'items.product.unit']);
        $settings = auth()->user()->tenant?->businessSetting;
        $currencySymbol = $settings?->currency_symbol ?? 'Rs';

        if (request()->wantsJson() || request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'order' => [
                    'id' => $order->id,
                    'order_no' => $order->order_no,
                    'restaurant_table_id' => $order->restaurant_table_id,
                    'user_id' => $order->user_id,
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
                    'status' => $order->status,
                    'is_paid' => $order->is_paid,
                    'grand_total' => (float) $order->grand_total,
                    'tip_amount' => (float) $order->tip_amount,
                    'items' => $order->items->map(function ($item) {
                        return [
                            'product_id' => $item->product_id,
                            'product' => [
                                'name' => $item->product->name ?? 'Unknown',
                                'unit' => $item->product->unit ? ['short_code' => $item->product->unit->short_code] : null,
                            ],
                            'qty' => (float) $item->qty,
                            'unit_price' => (float) $item->unit_price,
                            'special_instructions' => $item->special_instructions,
                        ];
                    })->toArray(),
                ],
            ]);
        }

        return view('restaurant.orders.show', compact('order', 'currencySymbol'));
    }

    public function edit(RestaurantOrder $order)
    {
        //
    }

    public function update(Request $request, RestaurantOrder $order)
    {
        //
    }

    public function destroy(RestaurantOrder $order)
    {
        //
    }

    public function updateStatus(Request $request, RestaurantOrder $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,served,completed,cancelled',
        ]);

        $status = $validated['status'];
        $updateData = ['status' => $status];

        switch ($status) {
            case 'confirmed':
                $updateData['confirmed_at'] = now();
                break;
            case 'preparing':
                $updateData['preparing_at'] = now();
                break;
            case 'ready':
                $updateData['ready_at'] = now();
                break;
            case 'served':
                $updateData['served_at'] = now();
                break;
            case 'completed':
                $updateData['completed_at'] = now();
                break;
        }

        $order->update($updateData);

        if (in_array($status, ['preparing', 'ready', 'served'])) {
            $itemStatus = match ($status) {
                'preparing' => 'preparing',
                'ready' => 'ready',
                'served' => 'served',
                default => 'pending'
            };
            $order->items()->update(['status' => $itemStatus]);
        }

        return response()->json(['success' => true, 'message' => 'Order status updated']);
    }

    public function splitBill(Request $request, RestaurantOrder $order)
    {
        $validated = $request->validate([
            'split_count' => 'required|integer|min:2|max:10',
        ]);

        $order->update([
            'is_split' => true,
            'split_count' => $validated['split_count'],
        ]);

        return response()->json(['success' => true, 'message' => 'Bill split successfully']);
    }

    public function pay(Request $request, RestaurantOrder $order)
    {
        if ($order->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'payment_method' => 'required|string|max:40',
            'tip_amount' => 'nullable|numeric|min:0',
            'tip_type' => 'nullable|in:fixed,percentage',
        ]);

        $user = auth()->user();
        $branchId = $user->branch_id;

        $sessionService = app(CashDrawerSessionService::class);
        $activeSession = $sessionService->getActiveSession($user->tenant_id, $branchId);

        $order->update([
            'status' => 'completed',
            'completed_at' => now(),
            'is_paid' => true,
            'paid_at' => now(),
            'payment_method' => $validated['payment_method'],
            'tip_amount' => $validated['tip_amount'] ?? 0,
            'tip_type' => $validated['tip_type'] ?? null,
            'cash_drawer_session_id' => $activeSession?->id,
        ]);

        $order->items()->update(['status' => 'served']);

        $this->reduceStockForOrder($order);

        if ($activeSession) {
            $paymentMethod = strtolower($validated['payment_method']);
            if ($paymentMethod === 'cash') {
                $activeSession->increment('cash_sales', $order->grand_total);
            } elseif ($paymentMethod === 'card') {
                $activeSession->increment('card_sales', $order->grand_total);
            } else {
                $activeSession->increment('other_sales', $order->grand_total);
            }
        }

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
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order payment processed successfully',
            'order' => $order->fresh(['items.product']),
        ]);
    }

    public function printKitchen(RestaurantOrder $order)
    {
        if ($order->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $order->load(['items.product', 'table', 'user']);

        $printService = app(PrintService::class);

        $data = [
            'order_no' => $order->order_no,
            'table' => $order->table?->name,
            'waiter' => $order->user?->name ?? 'Unknown',
            'items' => $order->items->map(function ($item) {
                return [
                    'name' => $item->product->name ?? 'Unknown',
                    'qty' => $item->qty,
                    'special_instructions' => $item->special_instructions,
                    'modifiers' => [],
                ];
            })->toArray(),
        ];

        $printed = $printService->printKitchenTicket($data);

        return response()->json([
            'success' => $printed,
            'message' => $printed ? 'Kitchen ticket printed' : 'Failed to print kitchen ticket',
        ]);
    }

    public function printReceipt(RestaurantOrder $order)
    {
        if ($order->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $order->load(['items.product']);

        $printService = app(PrintService::class);

        $data = [
            'invoice_no' => $order->order_no,
            'order_no' => $order->order_no,
            'items' => $order->items->map(function ($item) {
                return [
                    'name' => $item->product->name ?? 'Unknown',
                    'qty' => $item->qty,
                    'unit_price' => $item->unit_price,
                ];
            })->toArray(),
            'subtotal' => (float) $order->subtotal,
            'discount_total' => (float) $order->discount_total,
            'tax_total' => (float) $order->tax_total,
            'service_charge' => (float) $order->service_charge,
            'grand_total' => (float) $order->grand_total,
            'tip_amount' => (float) $order->tip_amount,
            'payment_method' => $order->is_paid ? ucfirst($order->payment_method ?? 'Cash') : 'Pending',
        ];

        $html = $printService->generateReceiptHtml($data);

        return response()->json([
            'success' => true,
            'html' => $html,
        ]);
    }

    protected function reduceStockForOrder(RestaurantOrder $order)
    {
        $tenantId = $order->tenant_id;
        $branchId = $order->branch_id;

        if (! $tenantId || ! $branchId) {
            \Log::warning('Cannot reduce stock: missing tenant_id or branch_id', [
                'order_id' => $order->id,
                'tenant_id' => $tenantId,
                'branch_id' => $branchId,
            ]);

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
}
