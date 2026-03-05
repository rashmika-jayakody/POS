<?php

namespace App\Http\Controllers;

use App\Models\RestaurantOrder;
use App\Models\RestaurantOrderItem;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
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
            'is_paid' => 'nullable|boolean', // true if paid, false if pay later
        ]);

        $user = auth()->user();
        
        // Determine status based on payment
        $isPaid = $validated['is_paid'] ?? false;
        $status = $isPaid ? 'completed' : 'confirmed';
        
        $orderData = [
            'tenant_id' => $user->tenant_id,
            'branch_id' => $user->branch_id,
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
        ];
        
        // Set timestamps based on status
        if ($status === 'confirmed') {
            $orderData['confirmed_at'] = now();
        } else {
            $orderData['completed_at'] = now();
        }
        
        $order = RestaurantOrder::create($orderData);

        // Create order items
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
                'status' => $isPaid ? 'served' : 'pending', // pending if not paid yet
            ]);
        }

        // Update table status if table is assigned
        if ($order->restaurant_table_id) {
            $table = RestaurantTable::find($order->restaurant_table_id);
            if ($table) {
                $table->update(['status' => 'occupied']);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Order saved successfully',
            'order_id' => $order->id,
        ]);
    }

    public function show(RestaurantOrder $order)
    {
        // Check if user has access to this order
        if ($order->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $order->load(['table', 'user', 'customer', 'items.product', 'items.product.unit', 'modifiers']);
        $settings = auth()->user()->tenant?->businessSetting;
        $currencySymbol = $settings?->currency_symbol ?? 'Rs';
        
        // Return JSON for AJAX requests or if Accept header includes application/json
        if (request()->wantsJson() || request()->expectsJson() || request()->ajax()) {
            try {
                return response()->json([
                    'success' => true,
                    'order' => [
                        'id' => $order->id,
                        'order_no' => $order->order_no,
                        'restaurant_table_id' => $order->restaurant_table_id,
                        'user_id' => $order->user_id,
                        'customer_name' => $order->customer_name ?? null,
                        'customer_phone' => $order->customer_phone ?? null,
                        'status' => $order->status,
                        'grand_total' => (float) $order->grand_total,
                        'items' => $order->items->map(function($item) {
                            return [
                                'product_id' => $item->product_id,
                                'product' => [
                                    'name' => $item->product->name ?? 'Unknown Product',
                                    'unit' => $item->product->unit ? ['short_code' => $item->product->unit->short_code] : null
                                ],
                                'qty' => (float) $item->qty,
                                'unit_price' => (float) $item->unit_price,
                            ];
                        })->toArray()
                    ]
                ]);
            } catch (\Exception $e) {
                \Log::error('Error loading order: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading order: ' . $e->getMessage()
                ], 500);
            }
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
        
        // Update timestamps based on status
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
        
        // Update order items status
        if (in_array($status, ['preparing', 'ready', 'served'])) {
            $itemStatus = match($status) {
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
        // Check if user has access to this order
        if ($order->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'payment_method' => 'required|string|max:40',
        ]);

        // Update order to completed status
        $order->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Update order items to served
        $order->items()->update(['status' => 'served']);

        // Update table status if table is assigned
        if ($order->restaurant_table_id) {
            $table = RestaurantTable::find($order->restaurant_table_id);
            if ($table) {
                $table->update(['status' => 'available']);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Order payment processed successfully',
        ]);
    }
}
