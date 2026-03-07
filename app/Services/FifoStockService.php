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
     * Deduct quantity from stock using FIFO (First In, First Out): oldest batches first.
     * 
     * Example:
     * - GRN 1: 20 units @ 100rs (received_at: 2024-01-01)
     * - GRN 2: 10 units @ 120rs (received_at: 2024-01-05)
     * 
     * When selling 25 units:
     * - First sells 20 units from GRN 1 @ 100rs = 2,000rs cost
     * - Then sells 5 units from GRN 2 @ 120rs = 600rs cost
     * - Total cost = 2,600rs
     * 
     * Updates both stock_batches and the aggregate stocks table.
     *
     * @param int $tenantId
     * @param int $productId
     * @param int $branchId
     * @param float $quantity Quantity to deduct
     * @return array|bool Returns array with 'success' => true and 'cost' => total cost, or false if insufficient stock
     */
    public function deduct(int $tenantId, int $productId, int $branchId, float $quantity)
    {
        $quantity = (float) $quantity;
        
        // Get batches ordered by received_at (oldest first), then by id (for batches received on same date)
        // This ensures FIFO: first batch received is sold first
        $batches = StockBatch::where('tenant_id', $tenantId)
            ->where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->where('quantity', '>', 0)
            ->orderBy('received_at', 'asc')  // Oldest batches first
            ->orderBy('id', 'asc')            // If same received_at, oldest ID first
            ->lockForUpdate()
            ->get();

        $totalAvailable = $batches->sum('quantity');

        // Legacy: no batches yet (pre-FIFO data) — deduct from aggregate Stock only
        // Use product cost_price as fallback (not ideal, but handles legacy data)
        if ($batches->isEmpty()) {
            $stock = Stock::where('tenant_id', $tenantId)
                ->where('product_id', $productId)
                ->where('branch_id', $branchId)
                ->with('product')
                ->lockForUpdate()
                ->first();
            if (! $stock || (float) $stock->quantity < $quantity) {
                return false;
            }
            $stock->decrement('quantity', $quantity);
            // Return cost using product cost_price as fallback
            $fallbackCost = (float) ($stock->product->cost_price ?? 0);
            return [
                'success' => true,
                'cost' => $fallbackCost * $quantity,
            ];
        }

        // Check if we have enough stock
        if ($totalAvailable < $quantity) {
            return false;
        }

        // Calculate total cost using FIFO: consume oldest batches first
        $totalCost = 0.0;
        $remaining = $quantity;
        
        DB::transaction(function () use ($batches, $quantity, $tenantId, $productId, $branchId, &$remaining, &$totalCost) {
            foreach ($batches as $batch) {
                if ($remaining <= 0) {
                    break;
                }
                
                // Calculate how much to deduct from this batch
                $deductFromBatch = min($remaining, (float) $batch->quantity);
                
                // Calculate cost for this portion: batch purchase_price * quantity deducted
                $batchCost = (float) ($batch->purchase_price ?? 0);
                $totalCost += $batchCost * $deductFromBatch;
                
                // Deduct from batch
                $batch->decrement('quantity', $deductFromBatch);
                $remaining -= $deductFromBatch;
            }

            // Update aggregate stock table
            $stock = Stock::where('tenant_id', $tenantId)
                ->where('product_id', $productId)
                ->where('branch_id', $branchId)
                ->lockForUpdate()
                ->first();

            if ($stock) {
                $stock->decrement('quantity', $quantity);
            }
        });

        return [
            'success' => true,
            'cost' => $totalCost,  // Total cost = sum of (batch_purchase_price * quantity_from_each_batch)
        ];
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
        ?int $grnItemId = null,
        ?float $purchasePrice = null
    ): StockBatch {
        return DB::transaction(function () use ($tenantId, $productId, $branchId, $quantity, $batchNumber, $expiryDate, $grnItemId, $purchasePrice) {
            // If purchase price not provided, use product cost_price as fallback
            if ($purchasePrice === null) {
                $product = \App\Models\Product::find($productId);
                $purchasePrice = $product ? (float) ($product->cost_price ?? 0) : 0;
            }
            
            $batch = StockBatch::create([
                'tenant_id' => $tenantId,
                'product_id' => $productId,
                'branch_id' => $branchId,
                'batch_number' => $batchNumber,
                'quantity' => $quantity,
                'purchase_price' => $purchasePrice,
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
