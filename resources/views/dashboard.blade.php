@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@push('styles')
<style>
    @media (max-width: 900px) { .dashboard-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } .dashboard-charts { grid-template-columns: 1fr; } }
    @media (max-width: 480px) { .dashboard-stats { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
    <div class="page-header animate-in">
        <div class="page-title">
            <i class="fas fa-tachometer-alt"></i>
            Dashboard
        </div>
        <div class="page-subtitle">
            Welcome back, {{ auth()->user()->name }}! Here's your business overview.
            @if($isSystemOwner ?? false)
                <span style="display: inline-block; margin-left: 8px; padding: 2px 8px; background: rgba(74, 158, 255, 0.15); color: #4A9EFF; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">All tenants</span>
            @endif
        </div>
    </div>

    <!-- Stats -->
    <div class="dashboard-stats animate-in" style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin-bottom: 24px;">
        <div style="background: #fff; border-radius: 12px; padding: 14px 16px; border: 1px solid rgba(74, 158, 255, 0.1); box-shadow: 0 2px 8px rgba(10, 26, 61, 0.08); box-sizing: border-box; min-width: 0;">
            <div style="width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.95rem; margin-bottom: 10px; background: rgba(74, 158, 255, 0.15); color: #4A9EFF;"><i class="fas fa-dollar-sign"></i></div>
            <div style="font-size: 0.72rem; color: #64748B; font-weight: 600; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-transform: uppercase; letter-spacing: 0.3px;">Sales this week</div>
            <div style="font-size: 1.25rem; font-weight: 800; color: #0F172A; letter-spacing: -0.5px; word-break: break-all;">{{ $currencySymbol ?? 'Rs' }}{{ number_format($salesThisWeek ?? 0, 0) }}</div>
            <div style="font-size: 0.7rem; margin-top: 6px; display: flex; align-items: center; gap: 4px; flex-wrap: wrap; color: {{ ($salesChange ?? 0) >= 0 ? '#10B981' : '#EF4444' }};">
                <i class="fas fa-arrow-{{ ($salesChange ?? 0) >= 0 ? 'up' : 'down' }}"></i>
                {{ $salesChange ?? 0 }}% vs last week
            </div>
        </div>
        <div style="background: #fff; border-radius: 12px; padding: 14px 16px; border: 1px solid rgba(74, 158, 255, 0.1); box-shadow: 0 2px 8px rgba(10, 26, 61, 0.08); box-sizing: border-box; min-width: 0;">
            <div style="width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.95rem; margin-bottom: 10px; background: rgba(16, 185, 129, 0.15); color: #10B981;"><i class="fas fa-users"></i></div>
            <div style="font-size: 0.72rem; color: #64748B; font-weight: 600; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-transform: uppercase; letter-spacing: 0.3px;">Users</div>
            <div style="font-size: 1.25rem; font-weight: 800; color: #0F172A; letter-spacing: -0.5px; word-break: break-all;">{{ number_format($usersCount ?? 0) }}</div>
            <div style="font-size: 0.7rem; margin-top: 6px; display: flex; align-items: center; gap: 4px; color: #10B981;"><i class="fas fa-user"></i> In your store</div>
        </div>
        <div style="background: #fff; border-radius: 12px; padding: 14px 16px; border: 1px solid rgba(74, 158, 255, 0.1); box-shadow: 0 2px 8px rgba(10, 26, 61, 0.08); box-sizing: border-box; min-width: 0;">
            <div style="width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.95rem; margin-bottom: 10px; background: rgba(245, 158, 11, 0.15); color: #F59E0B;"><i class="fas fa-box"></i></div>
            <div style="font-size: 0.72rem; color: #64748B; font-weight: 600; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-transform: uppercase; letter-spacing: 0.3px;">Units in stock</div>
            <div style="font-size: 1.25rem; font-weight: 800; color: #0F172A; letter-spacing: -0.5px; word-break: break-all;">{{ number_format($inStock ?? 0) }}</div>
            <div style="font-size: 0.7rem; margin-top: 6px;"><a href="{{ route('reports.stock-valuation') }}" style="color: #10B981; text-decoration: none;"><i class="fas fa-chart-line"></i> Stock report</a></div>
        </div>
        <div style="background: #fff; border-radius: 12px; padding: 14px 16px; border: 1px solid rgba(74, 158, 255, 0.1); box-shadow: 0 2px 8px rgba(10, 26, 61, 0.08); box-sizing: border-box; min-width: 0;">
            <div style="width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.95rem; margin-bottom: 10px; background: rgba(239, 68, 68, 0.15); color: #EF4444;"><i class="fas fa-store"></i></div>
            <div style="font-size: 0.72rem; color: #64748B; font-weight: 600; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-transform: uppercase; letter-spacing: 0.3px;">Locations</div>
            <div style="font-size: 1.25rem; font-weight: 800; color: #0F172A; letter-spacing: -0.5px; word-break: break-all;">{{ number_format($locationsCount ?? 0) }}</div>
            @if(($expiryCount ?? 0) > 0)
                <div style="font-size: 0.7rem; margin-top: 6px;"><a href="{{ route('reports.expiry-tracking') }}" style="color: #EF4444; text-decoration: none;"><i class="fas fa-exclamation-triangle"></i> {{ $expiryCount }} expiring soon</a></div>
            @else
                <div style="font-size: 0.7rem; margin-top: 6px; color: #10B981;"><i class="fas fa-check"></i> Branches</div>
            @endif
        </div>
    </div>

    <!-- Charts row -->
    <div class="dashboard-charts animate-in" style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
        <div style="background: #fff; border-radius: 20px; padding: 24px; border: 1px solid rgba(74, 158, 255, 0.1); box-shadow: 0 2px 8px rgba(10, 26, 61, 0.08);">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: #0F172A; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;"><i class="fas fa-chart-bar" style="color: #4A9EFF;"></i> Sales last 7 days</h3>
            <p style="font-size: 0.8rem; color: #64748B; margin-bottom: 16px;">From <a href="{{ route('reports.sales-summary') }}" style="color: #4A9EFF; text-decoration: none;">Sales Summary</a></p>
            <div id="wrapSales" style="position: relative; height: 240px; width: 100%;">
                @if(!empty($salesByDay) && count($salesByDay) > 0)
                    <canvas id="chartSales"></canvas>
                @else
                    <div style="height: 200px; display: flex; align-items: center; justify-content: center; background: #F1F5F9; border-radius: 12px; color: #64748B; font-size: 0.9rem;">No sales data for the last 7 days</div>
                @endif
            </div>
        </div>
        <div style="background: #fff; border-radius: 20px; padding: 24px; border: 1px solid rgba(74, 158, 255, 0.1); box-shadow: 0 2px 8px rgba(10, 26, 61, 0.08);">
            <h3 style="font-size: 1.05rem; font-weight: 700; color: #0F172A; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;"><i class="fas fa-chart-pie" style="color: #4A9EFF;"></i> Expenses this month</h3>
            <p style="font-size: 0.8rem; color: #64748B; margin-bottom: 16px;">From <a href="{{ route('reports.profit-loss') }}" style="color: #4A9EFF; text-decoration: none;">Profit & Loss</a></p>
            <div id="wrapExpenses" style="position: relative; height: 240px; width: 100%;">
                @if(!empty($expensesByCategory) && count($expensesByCategory) > 0)
                    <canvas id="chartExpenses"></canvas>
                @else
                    <div style="height: 200px; display: flex; align-items: center; justify-content: center; background: #F1F5F9; border-radius: 12px; color: #64748B; font-size: 0.9rem;">No expenses this month</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Stock chart full width -->
    <div class="animate-in" style="background: #fff; border-radius: 20px; padding: 24px; border: 1px solid rgba(74, 158, 255, 0.1); box-shadow: 0 2px 8px rgba(10, 26, 61, 0.08); margin-bottom: 24px;">
        <h3 style="font-size: 1.05rem; font-weight: 700; color: #0F172A; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;"><i class="fas fa-boxes-stacked" style="color: #4A9EFF;"></i> Stock value by category</h3>
        <p style="font-size: 0.8rem; color: #64748B; margin-bottom: 16px;">From <a href="{{ route('reports.stock-valuation') }}" style="color: #4A9EFF; text-decoration: none;">Stock Valuation</a></p>
        <div id="wrapStock" style="position: relative; height: 280px; width: 100%;">
            @if(!empty($stockByCategory) && count($stockByCategory) > 0)
                <canvas id="chartStock"></canvas>
            @else
                <div style="height: 200px; display: flex; align-items: center; justify-content: center; background: #F1F5F9; border-radius: 12px; color: #64748B; font-size: 0.9rem;">No stock data</div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="section animate-in">
        <h2 class="section-title"><i class="fas fa-bolt"></i> Quick Actions</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px;">
            @php
                $posType = auth()->user()->tenant?->pos_type ?? 'retail';
            @endphp
            @if($posType === 'restaurant')
            <a href="{{ route('restaurant-cash-drawer.index') }}" class="btn btn-primary" style="justify-content: center;"><i class="fas fa-utensils"></i> Restaurant POS</a>
            @else
            <a href="{{ route('cash-drawer.index') }}" class="btn btn-primary" style="justify-content: center;"><i class="fas fa-cash-register"></i> Cash Drawer</a>
            @endif
            <a href="{{ route('reports.index') }}" class="btn btn-primary" style="justify-content: center;"><i class="fas fa-chart-line"></i> Reports</a>
            <a href="{{ route('users.create') }}" class="btn btn-secondary" style="justify-content: center;"><i class="fas fa-user-plus"></i> Add User</a>
            <a href="{{ route('products.index') }}" class="btn btn-secondary" style="justify-content: center;"><i class="fas fa-box-open"></i> Products</a>
            <a href="{{ route('company-other-expenses.index') }}" class="btn btn-secondary" style="justify-content: center;"><i class="fas fa-receipt"></i> Expenses</a>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function() {
    const currency = @json($currencySymbol ?? 'Rs');

    const salesData = @json($salesByDay ?? []);
    if (document.getElementById('chartSales') && salesData.length) {
        new Chart(document.getElementById('chartSales'), {
            type: 'bar',
            data: {
                labels: salesData.map(d => d.label),
                datasets: [{
                    label: 'Sales',
                    data: salesData.map(d => parseFloat(d.total)),
                    backgroundColor: 'rgba(74, 158, 255, 0.6)',
                    borderColor: 'rgb(74, 158, 255)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: function(ctx) { return currency + ' ' + (ctx.raw || 0).toLocaleString(); } } }
                },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    const expensesData = @json($expensesByCategory ?? []);
    if (document.getElementById('chartExpenses') && expensesData.length) {
        const colors = ['#4A9EFF', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16'];
        new Chart(document.getElementById('chartExpenses'), {
            type: 'doughnut',
            data: {
                labels: expensesData.map(d => d.label),
                datasets: [{ data: expensesData.map(d => parseFloat(d.total)), backgroundColor: colors.slice(0, expensesData.length), borderWidth: 1 }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                const t = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                const p = t ? ((ctx.raw / t) * 100).toFixed(1) : 0;
                                return ctx.label + ': ' + currency + ' ' + (ctx.raw || 0).toLocaleString() + ' (' + p + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    const stockData = @json($stockByCategory ?? []);
    if (document.getElementById('chartStock') && stockData.length) {
        new Chart(document.getElementById('chartStock'), {
            type: 'bar',
            data: {
                labels: stockData.map(d => d.label),
                datasets: [{
                    label: 'Cost value',
                    data: stockData.map(d => parseFloat(d.total)),
                    backgroundColor: 'rgba(16, 185, 129, 0.6)',
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: function(ctx) { return currency + ' ' + (ctx.raw || 0).toLocaleString(); } } }
                },
                scales: { x: { beginAtZero: true } }
            }
        });
    }
})();
</script>
@endpush
