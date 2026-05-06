<?php

namespace App\Http\Controllers;

use App\Models\CashDrawerSession;
use App\Models\Refund;
use App\Models\RestaurantOrder;
use App\Models\Sale;
use App\Services\PrintService;
use App\Services\RefundService;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    protected RefundService $refundService;

    protected PrintService $printService;

    public function __construct(RefundService $refundService, PrintService $printService)
    {
        $this->refundService = $refundService;
        $this->printService = $printService;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $tenantId = $user->tenant_id;
        $branchId = $user->branch_id;

        $query = Refund::where('tenant_id', $tenantId)
            ->with(['user', 'items.product']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [
                $request->from.' 00:00:00',
                $request->to.' 23:59:59',
            ]);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $refunds = $query->orderBy('created_at', 'desc')->paginate(20);
        $currencySymbol = $user->tenant?->businessSetting?->currency_symbol ?? 'Rs';

        return view('refunds.index', compact('refunds', 'currencySymbol'));
    }

    public function create(Request $request)
    {
        $type = $request->get('type', 'sale');
        $invoiceNo = $request->get('invoice_no');

        $originalSale = null;
        $originalOrder = null;
        $items = [];

        if ($type === 'sale' && $invoiceNo) {
            $originalSale = Sale::where('invoice_no', $invoiceNo)
                ->where('tenant_id', $request->user()->tenant_id)
                ->with(['items.product'])
                ->first();

            if ($originalSale) {
                $items = $originalSale->items->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name ?? 'Unknown',
                        'qty' => $item->qty,
                        'unit_price' => $item->unit_price,
                        'max_qty' => $item->qty,
                    ];
                });
            }
        } elseif ($type === 'restaurant_order' && $invoiceNo) {
            $originalOrder = RestaurantOrder::where('order_no', $invoiceNo)
                ->where('tenant_id', $request->user()->tenant_id)
                ->with(['items.product'])
                ->first();

            if ($originalOrder) {
                $items = $originalOrder->items->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name ?? 'Unknown',
                        'qty' => $item->qty,
                        'unit_price' => $item->unit_price,
                        'max_qty' => $item->qty,
                    ];
                });
            }
        }

        $currencySymbol = $request->user()->tenant?->businessSetting?->currency_symbol ?? 'Rs';
        $activeSession = CashDrawerSession::where('tenant_id', $request->user()->tenant_id)
            ->where('branch_id', $request->user()->branch_id)
            ->where('status', 'open')
            ->first();

        return view('refunds.create', compact('type', 'invoiceNo', 'originalSale', 'originalOrder', 'items', 'currencySymbol', 'activeSession'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:sale,restaurant_order',
            'original_sale_id' => 'nullable|required_if:type,sale|exists:sales,id',
            'original_order_id' => 'nullable|required_if:type,restaurant_order|exists:restaurant_orders,id',
            'reason' => 'required|in:damaged,wrong_item,customer_request,quality_issue,other',
            'reason_notes' => 'nullable|string|max:500',
            'refund_method' => 'required|in:cash,card,store_credit,original_method',
            'update_inventory' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:255',
        ]);

        $user = $request->user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            $branch = \App\Models\Branch::where('tenant_id', $user->tenant_id)->first();
            $branchId = $branch?->id;
        }

        $activeSession = CashDrawerSession::where('tenant_id', $user->tenant_id)
            ->where('branch_id', $branchId)
            ->where('status', 'open')
            ->first();

        $refund = $this->refundService->createRefund(
            $user->tenant_id,
            $branchId,
            $user->id,
            $validated['type'],
            $validated['original_sale_id'] ?? null,
            $validated['original_order_id'] ?? null,
            $validated['reason'],
            $validated['items'],
            $validated['refund_method'],
            $validated['reason_notes'] ?? null,
            $validated['update_inventory'] ?? true,
            $activeSession?->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Refund processed successfully.',
            'refund' => $refund->load(['items.product']),
        ]);
    }

    public function show(Refund $refund)
    {
        if ($refund->tenant_id !== request()->user()->tenant_id) {
            abort(403);
        }

        $refund->load(['user', 'branch', 'items.product']);
        $currencySymbol = request()->user()->tenant?->businessSetting?->currency_symbol ?? 'Rs';

        return view('refunds.show', compact('refund', 'currencySymbol'));
    }

    public function searchInvoice(Request $request)
    {
        $validated = $request->validate([
            'invoice_no' => 'required|string',
            'type' => 'required|in:sale,restaurant_order',
        ]);

        $user = $request->user();

        if ($validated['type'] === 'sale') {
            $sale = Sale::where('invoice_no', $validated['invoice_no'])
                ->where('tenant_id', $user->tenant_id)
                ->with(['items.product.unit'])
                ->first();

            if (! $sale) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'type' => 'sale',
                'invoice' => [
                    'id' => $sale->id,
                    'invoice_no' => $sale->invoice_no,
                    'sale_date' => $sale->sale_date->format('Y-m-d H:i:s'),
                    'grand_total' => $sale->grand_total,
                    'payment_method' => $sale->payment_method,
                    'items' => $sale->items->map(function ($item) {
                        return [
                            'product_id' => $item->product_id,
                            'product_name' => $item->product->name ?? 'Unknown',
                            'unit' => $item->product->unit?->short_code ?? 'unit',
                            'qty' => $item->qty,
                            'unit_price' => $item->unit_price,
                            'line_total' => $item->line_total,
                        ];
                    }),
                ],
            ]);
        } else {
            $order = RestaurantOrder::where('order_no', $validated['invoice_no'])
                ->where('tenant_id', $user->tenant_id)
                ->with(['items.product.unit'])
                ->first();

            if (! $order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'type' => 'restaurant_order',
                'invoice' => [
                    'id' => $order->id,
                    'order_no' => $order->order_no,
                    'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                    'grand_total' => $order->grand_total,
                    'payment_method' => $order->payment_method,
                    'items' => $order->items->map(function ($item) {
                        return [
                            'product_id' => $item->product_id,
                            'product_name' => $item->product->name ?? 'Unknown',
                            'unit' => $item->product->unit?->short_code ?? 'unit',
                            'qty' => $item->qty,
                            'unit_price' => $item->unit_price,
                            'line_total' => $item->line_total,
                        ];
                    }),
                ],
            ]);
        }
    }

    public function printReceipt(Refund $refund)
    {
        if ($refund->tenant_id !== request()->user()->tenant_id) {
            abort(403);
        }

        $refund->load(['items.product']);
        $currencySymbol = request()->user()->tenant?->businessSetting?->currency_symbol ?? 'Rs';

        $data = [
            'invoice_no' => $refund->refund_number,
            'order_no' => $refund->refund_number,
            'items' => $refund->items->map(function ($item) {
                return [
                    'name' => $item->product->name ?? 'Unknown',
                    'qty' => $item->qty,
                    'unit_price' => $item->unit_price,
                ];
            })->toArray(),
            'subtotal' => $refund->subtotal,
            'tax_total' => $refund->tax_total,
            'grand_total' => $refund->grand_total,
            'payment_method' => 'Refund - '.ucfirst($refund->refund_method),
        ];

        $html = $this->printService->generateReceiptHtml($data);

        return response()->json([
            'success' => true,
            'html' => $html,
        ]);
    }
}
