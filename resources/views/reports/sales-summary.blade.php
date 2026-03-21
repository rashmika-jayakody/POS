@extends('layouts.admin')

@section('title', __('Sales Summary'))

@section('content')
    <div class="page-header animate-in">
        <div class="page-title"><i class="fas fa-chart-line"></i> {{ __('Sales Summary') }}</div>
        <div class="page-subtitle">{{ __('Daily / weekly / monthly sales. Use filters and apply to refresh.') }}</div>
    </div>

    @include('reports.partials.filter', ['showDate' => true, 'showBranch' => true, 'showPeriod' => true, 'f' => $f, 'period' => $period, 'branches' => $branches, 'routeName' => 'reports.sales-summary'])

    <div class="section animate-in">
        <h2 class="section-title"><i class="fas fa-calculator"></i> {{ __('Summary') }}</h2>
        <div class="stats-grid" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); margin-bottom: 0;">
            <div class="stat-card blue" style="margin-bottom: 0;">
                <div class="stat-label">{{ __('Total Invoices') }}</div>
                <div class="stat-value">{{ number_format($summary['total_invoices']) }}</div>
            </div>
            <div class="stat-card green" style="margin-bottom: 0;">
                <div class="stat-label">{{ __('Total Sales') }}</div>
                <div class="stat-value">{{ $currencySymbol ?? 'Rs' }}{{ number_format($summary['total_sales'], 2) }}</div>
            </div>
        </div>
    </div>

    <div class="section animate-in">
        <h2 class="section-title"><i class="fas fa-table"></i> {{ __('By period') }}</h2>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Period') }}</th>
                        <th>{{ __('Invoices') }}</th>
                        <th>{{ __('Total Sales') }}</th>
                        <th>{{ __('Avg. Sale') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $r)
                        <tr>
                            <td>{{ $r['period'] }}</td>
                            <td>{{ number_format($r['invoices']) }}</td>
                            <td>{{ $currencySymbol ?? 'Rs' }}{{ number_format($r['total_sales'], 2) }}</td>
                            <td>{{ $currencySymbol ?? 'Rs' }}{{ number_format($r['avg_sale'] ?? 0, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align: center; color: var(--gray-500); padding: 24px;">{{ __('No sales in this period.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
