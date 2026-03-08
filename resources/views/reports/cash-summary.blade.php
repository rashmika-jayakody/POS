@extends('layouts.admin')

@section('title', __('Cash Summary'))

@section('content')
    <div class="page-header animate-in">
        <div class="page-title"><i class="fas fa-money-bill-wave"></i> {{ __('Cash Summary') }}</div>
        <div class="page-subtitle">{{ __('Payments by method for the selected period.') }}</div>
    </div>

    @include('reports.partials.filter', ['showDate' => true, 'showBranch' => true, 'f' => $f, 'branches' => $branches, 'routeName' => 'reports.cash-summary'])

    <div class="section animate-in">
        <h2 class="section-title"><i class="fas fa-calculator"></i> Total</h2>
        <p style="font-size: 1.5rem; font-weight: 800; color: var(--navy-dark);">{{ $currencySymbol ?? 'Rs' }}{{ number_format($summary['total'], 2) }}</p>
    </div>

    <div class="section animate-in">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Payment method') }}</th>
                        <th>{{ __('Count') }}</th>
                        <th>{{ __('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $r)
                        <tr>
                            <td>{{ $r['payment_method'] }}</td>
                            <td>{{ number_format($r['count']) }}</td>
                            <td>{{ $currencySymbol ?? 'Rs' }}{{ number_format($r['total'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" style="text-align: center; color: var(--gray-500); padding: 24px;">{{ __('No payments in this period.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
