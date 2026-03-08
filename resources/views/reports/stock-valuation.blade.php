@extends('layouts.admin')

@section('title', __('Stock Valuation'))

@section('content')
    <div class="page-header animate-in">
        <div class="page-title"><i class="fas fa-boxes-stacked"></i> {{ __('Stock Valuation') }}</div>
        <div class="page-subtitle">{{ __('Current inventory value at cost and selling price (batch-wise).') }}</div>
    </div>

    @include('reports.partials.filter', ['showDate' => false, 'showBranch' => true, 'f' => $f ?? [], 'branches' => $branches, 'routeName' => 'reports.stock-valuation'])

    <div class="section animate-in">
        <h2 class="section-title"><i class="fas fa-calculator"></i> Totals</h2>
        <div class="stats-grid" style="grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); margin-bottom: 0;">
            <div class="stat-card blue" style="margin-bottom: 0;">
                <div class="stat-label">Total Batches</div>
                <div class="stat-value">{{ number_format($totals['total_batches'] ?? 0, 0) }}</div>
            </div>
            <div class="stat-card purple" style="margin-bottom: 0;">
                <div class="stat-label">Total Products</div>
                <div class="stat-value">{{ number_format($totals['total_products'] ?? 0, 0) }}</div>
            </div>
            <div class="stat-card orange" style="margin-bottom: 0;">
                <div class="stat-label">Total Quantity</div>
                <div class="stat-value">{{ number_format($totals['total_qty'] ?? 0, 2) }}</div>
            </div>
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

    <!-- Product Summary Section -->
    <div class="section animate-in">
        <h2 class="section-title"><i class="fas fa-list"></i> {{ __('Product Summary') }}</h2>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Product') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Branch') }}</th>
                        <th>{{ __('Batches') }}</th>
                        <th>{{ __('Total Qty') }}</th>
                        <th>{{ __('Cost value') }}</th>
                        <th>{{ __('Retail value') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($summaryRows ?? [] as $r)
                        <tr>
                            <td><strong>{{ $r['product']->name ?? '-' }}</strong></td>
                            <td>{{ $r['product']->category->name ?? '-' }}</td>
                            <td>{{ $r['branch']->name ?? '-' }}</td>
                            <td>{{ $r['batch_count'] }}</td>
                            <td>{{ number_format($r['total_qty'], 2) }}</td>
                            <td><strong>{{ $currencySymbol ?? 'Rs' }}{{ number_format($r['total_cost_value'], 2) }}</strong></td>
                            <td><strong>{{ $currencySymbol ?? 'Rs' }}{{ number_format($r['total_retail_value'], 2) }}</strong></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align: center; color: var(--gray-500); padding: 24px;">No stock.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Batch-wise Details Section -->
    <div class="section animate-in">
        <h2 class="section-title"><i class="fas fa-layer-group"></i> {{ __('Batch-wise Details') }}</h2>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Product') }}</th>
                        <th>{{ __('Branch') }}</th>
                        <th>{{ __('Batch Number') }}</th>
                        <th>{{ __('Supplier') }}</th>
                        <th>{{ __('Received Date') }}</th>
                        <th>{{ __('Expiry Date') }}</th>
                        <th>{{ __('Qty') }}</th>
                        <th>{{ __('Purchase Price') }}</th>
                        <th>{{ __('Cost value') }}</th>
                        <th>{{ __('Retail value') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batchRows ?? [] as $r)
                        <tr>
                            <td>{{ $r['product']->name ?? '-' }}</td>
                            <td>{{ $r['branch']->name ?? '-' }}</td>
                            <td><code style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">{{ $r['batch_number'] ?? '-' }}</code></td>
                            <td>{{ $r['supplier']->name ?? '-' }}</td>
                            <td>{{ $r['received_at'] ? \Carbon\Carbon::parse($r['received_at'])->format('Y-m-d') : '-' }}</td>
                            <td>{{ $r['expiry_date'] ? \Carbon\Carbon::parse($r['expiry_date'])->format('Y-m-d') : '-' }}</td>
                            <td>{{ number_format($r['qty'], 2) }}</td>
                            <td>{{ $currencySymbol ?? 'Rs' }}{{ number_format($r['purchase_price'], 2) }}</td>
                            <td><strong>{{ $currencySymbol ?? 'Rs' }}{{ number_format($r['cost_value'], 2) }}</strong></td>
                            <td>{{ $currencySymbol ?? 'Rs' }}{{ number_format($r['retail_value'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="10" style="text-align: center; color: var(--gray-500); padding: 24px;">{{ __('No stock batches.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
