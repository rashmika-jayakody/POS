@extends('layouts.admin')

@section('title', 'Stock Valuation')

@section('content')
    <div class="page-header animate-in">
        <div class="page-title"><i class="fas fa-boxes-stacked"></i> Stock Valuation</div>
        <div class="page-subtitle">Current inventory value at cost and selling price.</div>
    </div>

    @include('reports.partials.filter', ['showDate' => false, 'showBranch' => true, 'f' => $f ?? [], 'branches' => $branches, 'routeName' => 'reports.stock-valuation'])

    <div class="section animate-in">
        <h2 class="section-title"><i class="fas fa-calculator"></i> Totals</h2>
        <div class="stats-grid" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); margin-bottom: 0;">
            <div class="stat-card blue" style="margin-bottom: 0;">
                <div class="stat-label">Cost value</div>
                <div class="stat-value">{{ $currencySymbol ?? 'Rs' }}{{ number_format($totals['cost_value'], 2) }}</div>
            </div>
            <div class="stat-card green" style="margin-bottom: 0;">
                <div class="stat-label">Retail value</div>
                <div class="stat-value">{{ $currencySymbol ?? 'Rs' }}{{ number_format($totals['retail_value'], 2) }}</div>
            </div>
        </div>
    </div>

    <div class="section animate-in">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Branch</th>
                        <th>Qty</th>
                        <th>Cost value</th>
                        <th>Retail value</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $r)
                        <tr>
                            <td>{{ $r['product']->name ?? '-' }}</td>
                            <td>{{ $r['product']->category->name ?? '-' }}</td>
                            <td>{{ $r['branch']->name ?? '-' }}</td>
                            <td>{{ number_format($r['qty'], 2) }}</td>
                            <td>{{ $currencySymbol ?? 'Rs' }}{{ number_format($r['cost_value'], 2) }}</td>
                            <td>{{ $currencySymbol ?? 'Rs' }}{{ number_format($r['retail_value'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align: center; color: var(--gray-500); padding: 24px;">No stock.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
