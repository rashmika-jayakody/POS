<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockBatch;
use Illuminate\Support\Facades\DB;

/**
 * FIFO (First In, First Out) stock operations.
 * Batches are consumed in order of received_at ascending.
 */
class FifoStockService
{
    /**
     * Deduct quantity from stock using FIFO: oldest batches first.
     * Updates both stock_batches and the aggregate stocks table.
     *
     * @return bool True if deduction succeeded (enough stock), false otherwise
     */
    public function deduct(int $tenantId, int $productId, int $branchId, float $quantity): bool
    {
        $remaining = (float) $quantity;
        $batches = StockBatch::where('tenant_id', $tenantId)
            ->where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->where('quantity', '>', 0)
            ->orderBy('received_at')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        $totalAvailable = $batches->sum('quantity');

        // Legacy: no batches yet (pre-FIFO data) — deduct from aggregate Stock only
        if ($batches->isEmpty()) {
            $stock = Stock::where('tenant_id', $tenantId)
                ->where('product_id', $productId)
                ->where('branch_id', $branchId)
                ->first();
            if (! $stock || (float) $stock->quantity < $quantity) {
                return false;
            }
            $stock->decrement('quantity', $quantity);
            return true;
        }

        if ($totalAvailable < $remaining) {
            return false;
        }

        $remaining = (float) $quantity;
        DB::transaction(function () use ($batches, $quantity, $tenantId, $productId, $branchId, &$remaining) {
            foreach ($batches as $batch) {
                if ($remaining <= 0) {
                    break;
                }
                $deduct = min($remaining, (float) $batch->quantity);
                $batch->decrement('quantity', $deduct);
                $remaining -= $deduct;
            }

            $stock = Stock::where('tenant_id', $tenantId)
                ->where('product_id', $productId)
                ->where('branch_id', $branchId)
                ->lockForUpdate()
                ->first();

            if ($stock) {
                $stock->decrement('quantity', (float) $quantity);
            }
        });

        return true;
    }

    /**
     * Add quantity as a new batch (e.g. on return). Keeps FIFO order (new batch = newest).
     * Also updates the aggregate stocks table.
     */
    public function addBatch(
        int $tenantId,
        int $productId,
        int $branchId,
        float $quantity,
        string $batchNumber,
        ?\DateTimeInterface $expiryDate = null,
        ?int $grnItemId = null
    ): StockBatch {
        return DB::transaction(function () use ($tenantId, $productId, $branchId, $quantity, $batchNumber, $expiryDate, $grnItemId) {
            $batch = StockBatch::create([
                'tenant_id' => $tenantId,
                'product_id' => $productId,
                'branch_id' => $branchId,
                'batch_number' => $batchNumber,
                'quantity' => $quantity,
                'received_at' => now(),
                'expiry_date' => $expiryDate,
                'grn_item_id' => $grnItemId,
            ]);

            $stock = Stock::firstOrCreate(
                [
                    'product_id' => $productId,
                    'branch_id' => $branchId,
                    'tenant_id' => $tenantId,
                ],
                [
                    'quantity' => 0,
                    'low_stock_threshold' => 10,
                ]
            );
            $stock->increment('quantity', $quantity);

            return $batch;
        });
    }

    /**
     * Get total quantity available across all batches for product/branch (for display).
     */
    public function getAvailableQuantity(int $productId, int $branchId): float
    {
        return (float) StockBatch::where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->sum('quantity');
    }
}
