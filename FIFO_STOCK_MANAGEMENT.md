# FIFO Stock Management - Implementation Guide

## Overview

The system uses **FIFO (First In, First Out)** inventory management to ensure that:
1. Stock is sold in the order it was received (oldest batches first)
2. Cost of goods sold (COGS) is calculated accurately based on actual purchase prices
3. Revenue and profit calculations are precise

## How FIFO Works

### Example Scenario

**Purchase 1 (GRN from ABC Supplier):**
- Date: 2024-01-01
- Quantity: 20 units
- Purchase Price: 100rs per unit
- Total Cost: 2,000rs

**Purchase 2 (GRN from ABC Supplier):**
- Date: 2024-01-05
- Quantity: 10 units
- Purchase Price: 120rs per unit
- Total Cost: 1,200rs

### Stock Batches Created

After receiving both GRNs, the system creates:

| Batch | Received Date | Quantity | Purchase Price | Total Value |
|-------|--------------|----------|----------------|-------------|
| Batch 1 | 2024-01-01 | 20 | 100rs | 2,000rs |
| Batch 2 | 2024-01-05 | 10 | 120rs | 1,200rs |
| **Total** | | **30** | | **3,200rs** |

### Selling Stock (FIFO Logic)

When you sell products, the system automatically:

1. **Sells from oldest batch first** (Batch 1: 20 units @ 100rs)
2. **Then sells from next batch** (Batch 2: 10 units @ 120rs)

#### Example Sale Scenarios

**Scenario 1: Sell 15 units**
- Sells 15 units from Batch 1 @ 100rs
- Cost: 15 × 100rs = **1,500rs**
- Remaining: Batch 1 (5 units), Batch 2 (10 units)

**Scenario 2: Sell 25 units**
- Sells 20 units from Batch 1 @ 100rs = 2,000rs
- Sells 5 units from Batch 2 @ 120rs = 600rs
- **Total Cost: 2,600rs**
- Average Cost per Unit: 2,600rs ÷ 25 = **104rs per unit**
- Remaining: Batch 2 (5 units)

**Scenario 3: Sell 30 units (all stock)**
- Sells 20 units from Batch 1 @ 100rs = 2,000rs
- Sells 10 units from Batch 2 @ 120rs = 1,200rs
- **Total Cost: 3,200rs**
- Average Cost per Unit: 3,200rs ÷ 30 = **106.67rs per unit**

## Implementation Details

### 1. GRN Processing (`GrnController::receive`)

When a GRN is received:
- Creates a `StockBatch` with:
  - `received_at` = GRN's `received_date` (ensures correct FIFO order)
  - `purchase_price` = GRN item's `unit_price` (actual cost)
  - `quantity` = GRN item's `quantity`
- Updates aggregate `Stock` table

### 2. Stock Deduction (`FifoStockService::deduct`)

When selling:
- Orders batches by `received_at` (oldest first), then by `id`
- Consumes stock from oldest batches first
- Calculates total cost: `sum(batch_purchase_price × quantity_deducted_from_batch)`
- Updates both `stock_batches` and `stocks` tables

### 3. Sale Processing (`CashDrawerController::processSale`)

When processing a sale:
- Validates stock availability
- Deducts stock using FIFO
- Stores `cost_price_at_sale` (average cost per unit) in `SaleItem`
- This cost is used for:
  - COGS calculation
  - Profit/Loss reports
  - Revenue analysis

## Cost Calculation Formula

For each sale item:
```
Total Cost = Σ(batch_purchase_price × quantity_from_batch)
Average Cost per Unit = Total Cost ÷ Quantity Sold
```

**Example (Selling 25 units):**
```
Batch 1: 20 units × 100rs = 2,000rs
Batch 2: 5 units × 120rs = 600rs
─────────────────────────────────
Total Cost = 2,600rs
Average Cost = 2,600rs ÷ 25 = 104rs per unit
```

## Revenue and Profit Calculation

**Revenue:**
```
Revenue = Selling Price × Quantity Sold
```

**COGS (Cost of Goods Sold):**
```
COGS = cost_price_at_sale × Quantity Sold
     = (FIFO Total Cost) × Quantity Sold
```

**Gross Profit:**
```
Gross Profit = Revenue - COGS
```

**Example:**
- Sell 25 units @ 150rs each
- Revenue: 25 × 150rs = 3,750rs
- COGS (from FIFO): 2,600rs
- **Gross Profit: 3,750rs - 2,600rs = 1,150rs**

## Benefits of FIFO

1. **Accurate Cost Tracking**: Uses actual purchase prices, not averages
2. **Proper Inventory Valuation**: Oldest stock is sold first
3. **Precise Profit Calculation**: COGS reflects actual costs
4. **Compliance**: Follows standard accounting practices
5. **Better Decision Making**: Accurate cost data for pricing decisions

## Database Tables

### `stock_batches`
- Stores individual batches with purchase prices
- Ordered by `received_at` for FIFO

### `stocks`
- Aggregate stock quantity per product/branch
- Updated when batches are created/deducted

### `sale_items`
- Stores `cost_price_at_sale` (average FIFO cost per unit)
- Used for COGS in reports

## Important Notes

1. **Batch Ordering**: Batches are ordered by `received_at` (date from GRN), ensuring FIFO
2. **Cost Storage**: `cost_price_at_sale` stores average cost per unit for reporting
3. **Transaction Safety**: All stock operations use database transactions
4. **Stock Validation**: System validates stock availability before processing sales
5. **Restaurant Orders**: Also use FIFO when orders are paid/completed

## Testing the FIFO Logic

To verify FIFO is working:

1. Create GRN 1: 20 units @ 100rs (Date: 2024-01-01)
2. Create GRN 2: 10 units @ 120rs (Date: 2024-01-05)
3. Receive both GRNs
4. Make a sale for 25 units
5. Check `sale_items.cost_price_at_sale` should be approximately 104rs
   - (20 × 100 + 5 × 120) ÷ 25 = 2,600 ÷ 25 = 104rs
6. Verify remaining stock: Batch 2 should have 5 units remaining

---

**Last Updated**: 2024-03-07
**Status**: ✅ Implemented and Verified
