@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
    <div class="page-header animate-in">
        <div class="page-title">
            <i class="fas fa-chart-line"></i>
            Reports
        </div>
        <div class="page-subtitle">Choose a report to view.</div>
    </div>

    <div class="stats-grid animate-in" style="grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));">
        <a href="{{ route('reports.sales-summary') }}" class="stat-card blue" style="text-decoration: none; color: inherit;">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div class="stat-label">Sales Summary</div>
            <div class="stat-value" style="font-size: 1rem;">Daily / Weekly / Monthly</div>
        </a>
        <a href="{{ route('reports.profit-loss') }}" class="stat-card green" style="text-decoration: none; color: inherit;">
            <div class="stat-icon"><i class="fas fa-scale-balanced"></i></div>
            <div class="stat-label">Profit & Loss</div>
            <div class="stat-value" style="font-size: 1rem;">Profit or loss report</div>
        </a>
        <a href="{{ route('reports.itemwise-sales') }}" class="stat-card blue" style="text-decoration: none; color: inherit;">
            <div class="stat-icon"><i class="fas fa-list"></i></div>
            <div class="stat-label">Itemwise Sales</div>
            <div class="stat-value" style="font-size: 1rem;">Sales by item</div>
        </a>
        <a href="{{ route('reports.categorywise-sales') }}" class="stat-card green" style="text-decoration: none; color: inherit;">
            <div class="stat-icon"><i class="fas fa-tags"></i></div>
            <div class="stat-label">Categorywise Sales</div>
            <div class="stat-value" style="font-size: 1rem;">Sales by category</div>
        </a>
        <a href="{{ route('reports.cash-summary') }}" class="stat-card warning" style="text-decoration: none; color: inherit;">
            <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
            <div class="stat-label">Cash Summary</div>
            <div class="stat-value" style="font-size: 1rem;">Cash & payments</div>
        </a>
        <a href="{{ route('reports.expiry-tracking') }}" class="stat-card danger" style="text-decoration: none; color: inherit;">
            <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="stat-label">Expiry Tracking</div>
            <div class="stat-value" style="font-size: 1rem;">Expired / near expiry</div>
        </a>
        <a href="{{ route('reports.stock-valuation') }}" class="stat-card warning" style="text-decoration: none; color: inherit;">
            <div class="stat-icon"><i class="fas fa-boxes-stacked"></i></div>
            <div class="stat-label">Stock Valuation</div>
            <div class="stat-value" style="font-size: 1rem;">Inventory value</div>
        </a>
    </div>
@endsection
