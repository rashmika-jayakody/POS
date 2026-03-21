@extends('layouts.admin')

@section('title', __('Reports'))

@section('content')
    <div class="page-header animate-in">
        <div class="page-title">
            <i class="fas fa-chart-line"></i>
            {{ __('Reports') }}
        </div>
        <div class="page-subtitle">{{ __('Choose a report to view.') }}</div>
    </div>

    <div class="stats-grid animate-in" style="grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));">
        <a href="{{ route('reports.sales-summary') }}" class="stat-card blue" style="text-decoration: none; color: inherit;">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div class="stat-label">{{ __('Sales Summary') }}</div>
            <div class="stat-value" style="font-size: 1rem;">{{ __('Daily / Weekly / Monthly') }}</div>
        </a>
        <a href="{{ route('reports.profit-loss') }}" class="stat-card green" style="text-decoration: none; color: inherit;">
            <div class="stat-icon"><i class="fas fa-scale-balanced"></i></div>
            <div class="stat-label">{{ __('Profit & Loss') }}</div>
            <div class="stat-value" style="font-size: 1rem;">{{ __('Profit or loss report') }}</div>
        </a>
        <a href="{{ route('reports.itemwise-sales') }}" class="stat-card blue" style="text-decoration: none; color: inherit;">
            <div class="stat-icon"><i class="fas fa-list"></i></div>
            <div class="stat-label">{{ __('Itemwise Sales') }}</div>
            <div class="stat-value" style="font-size: 1rem;">{{ __('Sales by item') }}</div>
        </a>
        <a href="{{ route('reports.categorywise-sales') }}" class="stat-card green" style="text-decoration: none; color: inherit;">
            <div class="stat-icon"><i class="fas fa-tags"></i></div>
            <div class="stat-label">{{ __('Categorywise Sales') }}</div>
            <div class="stat-value" style="font-size: 1rem;">{{ __('Sales by category') }}</div>
        </a>
        <a href="{{ route('reports.cash-summary') }}" class="stat-card warning" style="text-decoration: none; color: inherit;">
            <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
            <div class="stat-label">{{ __('Cash Summary') }}</div>
            <div class="stat-value" style="font-size: 1rem;">{{ __('Cash & payments') }}</div>
        </a>
        <a href="{{ route('reports.expiry-tracking') }}" class="stat-card danger" style="text-decoration: none; color: inherit;">
            <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="stat-label">{{ __('Expiry Tracking') }}</div>
            <div class="stat-value" style="font-size: 1rem;">{{ __('Expired / near expiry') }}</div>
        </a>
        <a href="{{ route('reports.stock-valuation') }}" class="stat-card warning" style="text-decoration: none; color: inherit;">
            <div class="stat-icon"><i class="fas fa-boxes-stacked"></i></div>
            <div class="stat-label">{{ __('Stock Valuation') }}</div>
            <div class="stat-value" style="font-size: 1rem;">{{ __('Inventory value') }}</div>
        </a>
    </div>
@endsection
