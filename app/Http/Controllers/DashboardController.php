<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CompanyOtherExpense;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\StockBatch;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isSystemOwner = $user->hasRole('system_owner');
        $tenantId = $user->tenant_id;
        $currencySymbol = $user->tenant?->businessSetting?->currency_symbol ?? 'Rs';

        $scopeSale = $isSystemOwner ? Sale::withoutGlobalScope('tenant') : Sale::where('tenant_id', $tenantId);
        $scopeUser = $isSystemOwner ? User::query() : User::where('tenant_id', $tenantId);
        $scopeStock = $isSystemOwner ? Stock::withoutGlobalScope('tenant') : Stock::where('tenant_id', $tenantId);
        $scopeBranch = $isSystemOwner ? Branch::withoutGlobalScope('tenant') : Branch::where('tenant_id', $tenantId);
        $scopeExpense = $isSystemOwner ? CompanyOtherExpense::withoutGlobalScope('tenant') : CompanyOtherExpense::where('tenant_id', $tenantId);
        $scopeStockBatch = $isSystemOwner ? StockBatch::withoutGlobalScope('tenant') : StockBatch::where('tenant_id', $tenantId);

        // Sales: this week vs last week
        $thisWeekStart = now()->startOfWeek();
        $lastWeekStart = now()->subWeek()->startOfWeek();
        $salesThisWeek = (clone $scopeSale)->whereBetween('sale_date', [$thisWeekStart, now()])->sum('grand_total');
        $salesLastWeek = (clone $scopeSale)->whereBetween('sale_date', [$lastWeekStart, $lastWeekStart->copy()->endOfWeek()])->sum('grand_total');
        if ($salesLastWeek > 0) {
            $salesChange = round((($salesThisWeek - $salesLastWeek) / $salesLastWeek) * 100, 1);
        } elseif ($salesThisWeek > 0) {
            $salesChange = 100;
        } else {
            $salesChange = 0;
        }

        $usersCount = (clone $scopeUser)->count();
        $inStock = (clone $scopeStock)->sum('quantity');
        $locationsCount = (clone $scopeBranch)->count();

        // Chart: Sales last 7 days
        $salesByDay = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $salesByDay[] = [
                'label' => now()->subDays($i)->format('M j'),
                'total' => (clone $scopeSale)->whereDate('sale_date', $date)->sum('grand_total'),
            ];
        }

        $expensesByCategory = (clone $scopeExpense)
            ->businessExpenses()
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->get()
            ->groupBy('category')
            ->map(fn ($g, $cat) => ['label' => $cat, 'total' => $g->sum('amount')])
            ->sortByDesc('total')
            ->values();

        $stockByCategory = (clone $scopeStock)
            ->with('product.category', 'product')
            ->get()
            ->groupBy(fn ($s) => $s->product->category_id ?? 0)
            ->map(function ($stocks, $catId) {
                $name = $catId ? (optional($stocks->first()->product->category)->name ?? 'Uncategorized') : 'Uncategorized';
                // Calculate cost using FIFO batch costs (correct method)
                $costValue = $stocks->sum(function ($stock) {
                    $batches = \App\Models\StockBatch::where('tenant_id', $stock->tenant_id)
                        ->where('product_id', $stock->product_id)
                        ->where('branch_id', $stock->branch_id)
                        ->where('quantity', '>', 0)
                        ->get();
                    
                    if ($batches->isNotEmpty()) {
                        return $batches->sum(function ($batch) {
                            return (float) $batch->quantity * (float) ($batch->purchase_price ?? 0);
                        });
                    } else {
                        // Fallback to product cost_price for legacy data
                        return (float) $stock->quantity * (float) ($stock->product->cost_price ?? 0);
                    }
                });
                return ['label' => $name, 'total' => $costValue];
            })
            ->sortByDesc('total')
            ->take(8)
            ->values();

        $expiryCount = (clone $scopeStockBatch)
            ->where('quantity', '>', 0)
            ->whereDate('expiry_date', '<=', now()->addDays(30))
            ->count();

        return view('dashboard', compact(
            'currencySymbol',
            'salesThisWeek',
            'salesChange',
            'usersCount',
            'inStock',
            'locationsCount',
            'salesByDay',
            'expensesByCategory',
            'stockByCategory',
            'expiryCount',
            'isSystemOwner'
        ));
    }
}
