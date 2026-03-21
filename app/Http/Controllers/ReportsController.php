<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Category;
use App\Models\CompanyOtherExpense;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use App\Models\StockBatch;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    protected function isSystemOwner(): bool
    {
        return auth()->user()->hasRole('system_owner');
    }

    protected function getTenantId()
    {
        return auth()->user()->tenant_id;
    }

    protected function getBranches()
    {
        if ($this->isSystemOwner()) {
            return Branch::withoutGlobalScope('tenant')->orderBy('name')->get();
        }
        return Branch::where('tenant_id', $this->getTenantId())->orderBy('name')->get();
    }

    protected function getCurrencySymbol(): string
    {
        return auth()->user()->tenant?->businessSetting?->currency_symbol ?? 'Rs';
    }

    /** @return \Illuminate\Database\Eloquent\Builder */
    protected function baseSaleQuery()
    {
        return $this->isSystemOwner()
            ? Sale::withoutGlobalScope('tenant')
            : Sale::where('tenant_id', $this->getTenantId());
    }

    /** @return \Illuminate\Database\Eloquent\Builder */
    protected function baseExpenseQuery()
    {
        return $this->isSystemOwner()
            ? CompanyOtherExpense::withoutGlobalScope('tenant')
            : CompanyOtherExpense::where('tenant_id', $this->getTenantId());
    }

    /** @return \Illuminate\Database\Eloquent\Builder */
    protected function baseStockBatchQuery()
    {
        return $this->isSystemOwner()
            ? StockBatch::withoutGlobalScope('tenant')
            : StockBatch::where('tenant_id', $this->getTenantId());
    }

    /** @return \Illuminate\Database\Eloquent\Builder */
    protected function baseStockQuery()
    {
        return $this->isSystemOwner()
            ? Stock::withoutGlobalScope('tenant')
            : Stock::where('tenant_id', $this->getTenantId());
    }

    protected function getFilterDefaults(Request $request): array
    {
        $to = $request->get('to') ? Carbon::parse($request->get('to')) : now();
        $from = $request->get('from') ? Carbon::parse($request->get('from')) : now()->copy()->subDays(30);
        if ($from->gt($to)) {
            $from = $to->copy()->subDays(30);
        }
        return [
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
            'branch_id' => $request->get('branch_id'),
        ];
    }

    public function index()
    {
        return view('reports.index');
    }

    public function salesSummary(Request $request)
    {
        $branches = $this->getBranches();
        $f = $this->getFilterDefaults($request);

        $query = $this->baseSaleQuery()
            ->whereBetween('sale_date', [$f['from'] . ' 00:00:00', $f['to'] . ' 23:59:59']);
        if (!empty($f['branch_id'])) {
            $query->where('branch_id', $f['branch_id']);
        }

        $period = $request->get('period', 'day'); // day, week, month
        $dateFormat = $period === 'month' ? 'Y-m' : ($period === 'week' ? 'Y-W' : 'Y-m-d');
        $rows = (clone $query)->get()->groupBy(function ($sale) use ($dateFormat, $period) {
            $d = $sale->sale_date;
            if ($period === 'week') {
                return $d->format('Y') . '-W' . $d->weekOfYear;
            }
            if ($period === 'month') {
                return $d->format('Y-m');
            }
            return $d->format('Y-m-d');
        })->map(function ($group, $key) {
            return [
                'period' => $key,
                'invoices' => $group->count(),
                'total_sales' => $group->sum('grand_total'),
                'avg_sale' => $group->avg('grand_total'),
            ];
        })->sortKeys()->values();

        $summary = [
            'total_invoices' => (clone $query)->count(),
            'total_sales' => (clone $query)->sum('grand_total'),
        ];

        return view('reports.sales-summary', array_merge(compact('rows', 'summary', 'branches', 'f', 'period'), ['currencySymbol' => $this->getCurrencySymbol()]));
    }

    public function profitLoss(Request $request)
    {
        $branches = $this->getBranches();
        $f = $this->getFilterDefaults($request);

        $salesQuery = $this->baseSaleQuery()
            ->whereBetween('sale_date', [$f['from'] . ' 00:00:00', $f['to'] . ' 23:59:59']);
        if (!empty($f['branch_id'])) {
            $salesQuery->where('branch_id', $f['branch_id']);
        }
        $revenue = (clone $salesQuery)->sum('grand_total');
        $cogs = SaleItem::whereHas('sale', function ($q) use ($salesQuery) {
            $q->whereIn('id', (clone $salesQuery)->select('id'));
        })->get()->sum(function ($item) {
            return $item->cost_price_at_sale ? $item->cost_price_at_sale * $item->qty : 0;
        });
        $expensesQuery = $this->baseExpenseQuery()
            ->businessExpenses()
            ->whereBetween('expense_date', [$f['from'], $f['to']]);
        if (!empty($f['branch_id'])) {
            $expensesQuery->where('branch_id', $f['branch_id']);
        }
        $expenses = $expensesQuery->sum('amount');

        $rows = (clone $expensesQuery)->orderBy('expense_date')->get();

        $summary = [
            'revenue' => $revenue,
            'cogs' => $cogs,
            'gross_profit' => $revenue - $cogs,
            'expenses' => $expenses,
            'net_profit' => $revenue - $cogs - $expenses,
        ];

        return view('reports.profit-loss', array_merge(compact('summary', 'rows', 'branches', 'f'), ['currencySymbol' => $this->getCurrencySymbol()]));
    }

    public function itemwiseSales(Request $request)
    {
        $branches = $this->getBranches();
        $f = $this->getFilterDefaults($request);

        $saleIds = $this->baseSaleQuery()
            ->whereBetween('sale_date', [$f['from'] . ' 00:00:00', $f['to'] . ' 23:59:59']);
        if (!empty($f['branch_id'])) {
            $saleIds->where('branch_id', $f['branch_id']);
        }
        $saleIds = $saleIds->pluck('id');

        $rows = SaleItem::whereIn('sale_id', $saleIds)
            ->with('product.category', 'product.unit')
            ->get()
            ->groupBy('product_id')
            ->map(function ($items, $productId) {
                $p = $items->first()->product;
                $qty = $items->sum('qty');
                $net = $items->sum('line_total');
                $cogs = $items->sum(fn ($i) => $i->cost_price_at_sale ? $i->cost_price_at_sale * $i->qty : 0);
                return [
                    'product' => $p,
                    'total_qty' => $qty,
                    'net_sales' => $net,
                    'cogs' => $cogs,
                    'profit' => $net - $cogs,
                ];
            })->sortByDesc('net_sales')->values();

        return view('reports.itemwise-sales', array_merge(compact('rows', 'branches', 'f'), ['currencySymbol' => $this->getCurrencySymbol()]));
    }

    public function categorywiseSales(Request $request)
    {
        $branches = $this->getBranches();
        $f = $this->getFilterDefaults($request);

        $saleIds = $this->baseSaleQuery()
            ->whereBetween('sale_date', [$f['from'] . ' 00:00:00', $f['to'] . ' 23:59:59']);
        if (!empty($f['branch_id'])) {
            $saleIds->where('branch_id', $f['branch_id']);
        }
        $saleIds = $saleIds->pluck('id');

        $rows = SaleItem::whereIn('sale_id', $saleIds)
            ->with('product.category')
            ->get()
            ->groupBy(fn ($i) => $i->product->category_id ?? 0)
            ->map(function ($items, $catId) {
                $cat = $catId ? $items->first()->product->category : null;
                $qty = $items->sum('qty');
                $net = $items->sum('line_total');
                $cogs = $items->sum(fn ($i) => $i->cost_price_at_sale ? $i->cost_price_at_sale * $i->qty : 0);
                return [
                    'category' => $cat,
                    'category_name' => $cat ? $cat->name : 'Uncategorized',
                    'total_qty' => $qty,
                    'net_sales' => $net,
                    'cogs' => $cogs,
                    'profit' => $net - $cogs,
                ];
            })->sortByDesc('net_sales')->values();

        return view('reports.categorywise-sales', array_merge(compact('rows', 'branches', 'f'), ['currencySymbol' => $this->getCurrencySymbol()]));
    }

    public function cashSummary(Request $request)
    {
        $branches = $this->getBranches();
        $f = $this->getFilterDefaults($request);

        $query = $this->baseSaleQuery()
            ->whereBetween('sale_date', [$f['from'] . ' 00:00:00', $f['to'] . ' 23:59:59']);
        if (!empty($f['branch_id'])) {
            $query->where('branch_id', $f['branch_id']);
        }
        $rows = (clone $query)->get()->groupBy('payment_method')->map(function ($group, $method) {
            return [
                'payment_method' => $method ?: 'Cash',
                'count' => $group->count(),
                'total' => $group->sum('grand_total'),
            ];
        })->sortByDesc('total')->values();

        $summary = ['total' => (clone $query)->sum('grand_total')];

        return view('reports.cash-summary', array_merge(compact('rows', 'summary', 'branches', 'f'), ['currencySymbol' => $this->getCurrencySymbol()]));
    }

    public function expiryTracking(Request $request)
    {
        $branches = $this->getBranches();
        $branchId = $request->get('branch_id');
        $type = $request->get('type', 'expired'); // expired, soon
        $f = ['branch_id' => $branchId];

        $query = $this->baseStockBatchQuery()->where('quantity', '>', 0);
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        if ($type === 'expired') {
            $query->whereDate('expiry_date', '<', now()->toDateString());
        } else {
            $query->whereBetween('expiry_date', [now()->toDateString(), now()->addDays(30)->toDateString()]);
        }
        $rows = $query->with('product', 'branch')->orderBy('expiry_date')->get();

        return view('reports.expiry-tracking', compact('rows', 'branches', 'type', 'f'));
    }

    public function stockValuation(Request $request)
    {
        $branches = $this->getBranches();
        $branchId = $request->get('branch_id');
        $f = ['branch_id' => $branchId];

        // Get all batches with stock
        $batchQuery = $this->baseStockBatchQuery()
            ->where('quantity', '>', 0)
            ->with('product.category', 'product.unit', 'branch', 'grnItem.grn.supplier')
            ->orderBy('product_id')
            ->orderBy('branch_id')
            ->orderBy('received_at', 'asc')
            ->orderBy('id', 'asc');
        
        if ($branchId) {
            $batchQuery->where('branch_id', $branchId);
        }
        
        $batches = $batchQuery->get();
        
        // Group batches by product and branch for summary
        $productSummary = [];
        $batchRows = [];
        
        foreach ($batches as $batch) {
            $qty = (float) $batch->quantity;
            $purchasePrice = (float) ($batch->purchase_price ?? 0);
            $costValue = $qty * $purchasePrice;
            $sellingPrice = (float) ($batch->product->selling_price ?? 0);
            $retailValue = $qty * $sellingPrice;
            
            $key = $batch->product_id . '_' . $batch->branch_id;
            
            // Add to batch rows
            $batchRows[] = [
                'batch' => $batch,
                'product' => $batch->product,
                'branch' => $batch->branch,
                'batch_number' => $batch->batch_number,
                'received_at' => $batch->received_at,
                'expiry_date' => $batch->expiry_date,
                'supplier' => $batch->grnItem?->grn?->supplier,
                'qty' => $qty,
                'purchase_price' => $purchasePrice,
                'cost_value' => $costValue,
                'retail_value' => $retailValue,
            ];
            
            // Aggregate by product/branch
            if (!isset($productSummary[$key])) {
                $productSummary[$key] = [
                    'product' => $batch->product,
                    'branch' => $batch->branch,
                    'total_qty' => 0,
                    'total_cost_value' => 0,
                    'total_retail_value' => 0,
                    'batch_count' => 0,
                ];
            }
            
            $productSummary[$key]['total_qty'] += $qty;
            $productSummary[$key]['total_cost_value'] += $costValue;
            $productSummary[$key]['total_retail_value'] += $retailValue;
            $productSummary[$key]['batch_count']++;
        }
        
        // Convert summary to array and sort
        $summaryRows = collect($productSummary)->values()->sortByDesc(function ($item) {
            return $item['total_cost_value'];
        })->values();
        
        // Calculate totals
        $totals = [
            'total_batches' => count($batchRows),
            'total_products' => count($productSummary),
            'total_qty' => $batches->sum('quantity'),
            'cost_value' => $batches->sum(function ($b) {
                return (float) $b->quantity * (float) ($b->purchase_price ?? 0);
            }),
            'retail_value' => $batches->sum(function ($b) {
                return (float) $b->quantity * (float) ($b->product->selling_price ?? 0);
            }),
        ];

        return view('reports.stock-valuation', array_merge(compact('batchRows', 'summaryRows', 'totals', 'branches', 'f'), ['currencySymbol' => $this->getCurrencySymbol()]));
    }
}
