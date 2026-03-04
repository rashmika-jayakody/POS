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

        $query = $this->baseStockQuery()->with('product.category', 'product.unit', 'branch');
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        $rows = $query->get()->map(function ($stock) {
            $qty = (float) $stock->quantity;
            $cost = (float) ($stock->product->cost_price ?? 0);
            $selling = (float) ($stock->product->selling_price ?? 0);
            return [
                'stock' => $stock,
                'product' => $stock->product,
                'branch' => $stock->branch,
                'qty' => $qty,
                'cost_value' => $qty * $cost,
                'retail_value' => $qty * $selling,
            ];
        })->filter(fn ($r) => $r['qty'] > 0)->sortByDesc('cost_value')->values();

        $totals = [
            'cost_value' => $rows->sum('cost_value'),
            'retail_value' => $rows->sum('retail_value'),
        ];

        return view('reports.stock-valuation', array_merge(compact('rows', 'totals', 'branches', 'f'), ['currencySymbol' => $this->getCurrencySymbol()]));
    }
}
