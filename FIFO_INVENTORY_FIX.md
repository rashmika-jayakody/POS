# FIFO Inventory Accounting Fix

## Problem Identified

The system was incorrectly using `product.cost_price` for inventory valuation instead of using actual batch purchase costs from GRN (Goods Received Note). This breaks FIFO (First In, First Out) inventory accounting principles.

### Example of the Problem:
- **Batch 1**: 25 units @ 280 = 7,000
- **Batch 2**: 100 units @ 290 = 29,000
- **Total**: 125 units, correct value = 36,000

But the system was showing:
- 125 units × 280 (product cost_price) = 35,000 ❌

## Solution Implemented

### 1. Added `purchase_price` to StockBatch Table
- **Migration**: `2026_03_07_062201_add_purchase_price_to_stock_batches_table.php`
- Stores the actual purchase cost from GRN for each batch
- This is the **true cost** for FIFO calculations

### 2. Updated GRN Processing
- **File**: `app/Http/Controllers/GrnController.php`
- When creating StockBatch records, now stores `purchase_price` from `GrnItem.unit_price`
- Each batch now has its actual purchase cost

### 3. Updated FIFO Service
- **File**: `app/Services/FifoStockService.php`
- `deduct()` method now returns cost information:
  ```php
  return [
      'success' => true,
      'cost' => $totalCost, // Calculated from batch purchase prices
  ];
  ```
- Uses batch `purchase_price` for FIFO cost calculation
- Falls back to `product.cost_price` only for legacy data (pre-FIFO)

### 4. Fixed Stock Valuation Reports
- **File**: `app/Http/Controllers/ReportsController.php`
- `stockValuation()` method now calculates cost using:
  ```php
  // Correct: SUM(batch_qty × batch_purchase_price)
  $costValue = $batches->sum(function ($batch) {
      return (float) $batch->quantity * (float) ($batch->purchase_price ?? 0);
  });
  ```
- **Before**: Used `product.cost_price × total_qty` ❌
- **After**: Uses `SUM(batch_qty × batch_purchase_price)` ✅

### 5. Fixed Dashboard Stock Valuation
- **File**: `app/Http/Controllers/DashboardController.php`
- Stock by category chart now uses batch costs
- Consistent with reports

### 6. Updated StockBatch Model
- Added `purchase_price` to `$fillable`
- Added `purchase_price` to `$casts` as `decimal:2`

## How FIFO Now Works

### When Selling Items:
1. System calls `FifoStockService::deduct()`
2. Batches are consumed in order (oldest first)
3. Cost is calculated: `SUM(batch_qty_used × batch_purchase_price)`
4. This cost should be stored in `SaleItem.cost_price_at_sale`

### Inventory Valuation:
- **Correct Formula**: `SUM(batch_qty_remaining × batch_purchase_price)`
- **NOT**: `product_cost_price × total_qty`

### Example:
```
Batch 1: 25 units @ 280 (remaining: 0)
Batch 2: 100 units @ 290 (remaining: 95)

Inventory Value = (0 × 280) + (95 × 290) = 27,550 ✅
```

## Important Notes

### Product.cost_price Usage
- `product.cost_price` should now be treated as **reference only**
- It can be used as:
  - Last purchase price (for display)
  - Average cost (for reference)
  - Default for new products
- **BUT**: It should **NOT** be used for:
  - Inventory valuation ❌
  - COGS calculation ❌
  - Stock reports ❌

### Legacy Data Handling
- For products without batches (pre-FIFO data), system falls back to `product.cost_price`
- This is acceptable for backward compatibility
- New GRNs will create batches with proper costs

### When Saving Sales
When implementing sales save functionality, ensure:
1. Call `FifoStockService::deduct()` for each item
2. Store the returned `cost` in `SaleItem.cost_price_at_sale`
3. This ensures accurate COGS in profit/loss reports

Example:
```php
$fifo = app(FifoStockService::class);
$result = $fifo->deduct($tenantId, $productId, $branchId, $qty);

if ($result['success']) {
    SaleItem::create([
        'sale_id' => $sale->id,
        'product_id' => $productId,
        'qty' => $qty,
        'unit_price' => $sellingPrice,
        'line_total' => $qty * $sellingPrice,
        'cost_price_at_sale' => $result['cost'] / $qty, // Average cost per unit
    ]);
}
```

## Testing Checklist

- [x] GRN creates batches with purchase_price
- [x] Stock valuation report uses batch costs
- [x] Dashboard stock chart uses batch costs
- [x] FIFO service returns cost information
- [ ] Sales save uses FIFO costs (when implemented)
- [ ] Profit/Loss reports use correct COGS
- [ ] Returns create batches with appropriate cost

## Files Modified

1. `database/migrations/2026_03_07_062201_add_purchase_price_to_stock_batches_table.php` (NEW)
2. `app/Models/StockBatch.php`
3. `app/Http/Controllers/GrnController.php`
4. `app/Services/FifoStockService.php`
5. `app/Http/Controllers/ReportsController.php`
6. `app/Http/Controllers/DashboardController.php`

## Next Steps

1. **Update Sales Save Endpoint** (when implemented):
   - Use `FifoStockService::deduct()` to get costs
   - Store costs in `SaleItem.cost_price_at_sale`

2. **Update Product Management**:
   - Add note that `cost_price` is reference only
   - Optionally auto-update from last GRN purchase

3. **Migration for Existing Data**:
   - Consider migrating existing batches to have `purchase_price`
   - Can use `product.cost_price` as initial value

---

**Date**: 2026-03-07
**Status**: ✅ Core fixes implemented
