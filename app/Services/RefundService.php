<?php

namespace App\Services;

use App\Models\CashDrawerSession;
use App\Models\Refund;
use App\Models\RefundItem;
use App\Models\RestaurantOrder;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class RefundService
{
    public function __construct(
        protected FifoStockService $fifoStockService
    ) {}

    public function createRefund(
        int $tenantId,
        int $branchId,
        int $userId,
        string $type,
        ?int $originalSaleId,
        ?int $originalOrderId,
        string $reason,
        array $items,
        string $refundMethod = 'cash',
        ?string $reasonNotes = null,
        bool $updateInventory = true,
        ?int $cashDrawerSessionId = null
    ): Refund {
        return DB::transaction(function () use (
            $tenantId, $branchId, $userId, $type, $originalSaleId, $originalOrderId,
            $reason, $items, $refundMethod, $reasonNotes, $updateInventory, $cashDrawerSessionId
        ) {
            $originalInvoiceNo = null;
            $subtotal = 0;
            $taxTotal = 0;

            if ($type === 'sale' && $originalSaleId) {
                $sale = Sale::find($originalSaleId);
                $originalInvoiceNo = $sale?->invoice_no;
            } elseif ($type === 'restaurant_order' && $originalOrderId) {
                $order = RestaurantOrder::find($originalOrderId);
                $originalInvoiceNo = $order?->order_no;
            }

            $refund = Refund::create([
                'tenant_id' => $tenantId,
                'branch_id' => $branchId,
                'user_id' => $userId,
                'refund_number' => Refund::generateRefundNumber(),
                'type' => $type,
                'original_sale_id' => $originalSaleId,
                'original_order_id' => $originalOrderId,
                'original_invoice_no' => $originalInvoiceNo,
                'reason' => $reason,
                'reason_notes' => $reasonNotes,
                'subtotal' => 0,
                'tax_total' => 0,
                'grand_total' => 0,
                'refund_method' => $refundMethod,
                'inventory_updated' => false,
                'cash_drawer_session_id' => $cashDrawerSessionId,
            ]);

            foreach ($items as $item) {
                $lineTotal = (float) $item['qty'] * (float) $item['unit_price'];
                $subtotal += $lineTotal;

                $costPriceAtRefund = null;
                if ($updateInventory) {
                    $costPriceAtRefund = $this->getCostPrice($item['product_id'], $tenantId, $branchId);
                }

                RefundItem::create([
                    'refund_id' => $refund->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $lineTotal,
                    'cost_price_at_refund' => $costPriceAtRefund,
                    'restocked' => false,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            $grandTotal = $subtotal + $taxTotal;

            $refund->update([
                'subtotal' => $subtotal,
                'tax_total' => $taxTotal,
                'grand_total' => $grandTotal,
            ]);

            if ($updateInventory) {
                $this->restockItems($refund, $tenantId, $branchId);
            }

            if ($cashDrawerSessionId) {
                $this->updateSessionRefundTotal($cashDrawerSessionId, $grandTotal);
            }

            ActivityLogService::log('refund_processed', "Refund {$refund->refund_number} processed", [
                'refund_id' => $refund->id,
                'refund_number' => $refund->refund_number,
                'type' => $type,
                'grand_total' => $grandTotal,
                'reason' => $reason,
                'inventory_updated' => $updateInventory,
            ]);

            return $refund->fresh(['items.product']);
        });
    }

    protected function getCostPrice(int $productId, int $tenantId, int $branchId): ?float
    {
        $product = \App\Models\Product::find($productId);

        return $product ? (float) $product->cost_price : null;
    }

    protected function restockItems(Refund $refund, int $tenantId, int $branchId): void
    {
        foreach ($refund->items as $item) {
            $batchNumber = 'REF-'.$refund->refund_number.'-'.$item->id;

            $this->fifoStockService->addBatch(
                $tenantId,
                $item->product_id,
                $branchId,
                (float) $item->qty,
                $batchNumber,
                null,
                null,
                $item->cost_price_at_refund
            );

            $item->update(['restocked' => true]);
        }

        $refund->update(['inventory_updated' => true]);
    }

    protected function updateSessionRefundTotal(int $sessionId, float $amount): void
    {
        $session = CashDrawerSession::find($sessionId);
        if ($session && $session->isOpen()) {
            $session->increment('refunds_total', $amount);
        }
    }

    public function getRefundsByDateRange(int $tenantId, int $branchId, string $from, string $to)
    {
        return Refund::where('tenant_id', $tenantId)
            ->where('branch_id', $branchId)
            ->whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->with(['user', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getRefundStats(int $tenantId, int $branchId, string $from, string $to): array
    {
        $refunds = $this->getRefundsByDateRange($tenantId, $branchId, $from, $to);

        return [
            'total_refunds' => $refunds->count(),
            'total_amount' => $refunds->sum('grand_total'),
            'by_reason' => $refunds->groupBy('reason')->map->count(),
            'by_type' => $refunds->groupBy('type')->map->count(),
            'inventory_restocked' => $refunds->where('inventory_updated', true)->sum('grand_total'),
        ];
    }
}
