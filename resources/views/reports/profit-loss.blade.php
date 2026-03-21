@extends('layouts.admin')

@section('title', __('Profit & Loss'))

@section('content')
    <div class="page-header animate-in">
        <div class="page-title"><i class="fas fa-scale-balanced"></i> {{ __('Profit & Loss') }}</div>
        <div class="page-subtitle">{{ __('Revenue, COGS, expenses and net profit for the selected period.') }}</div>
    </div>

    @include('reports.partials.filter', ['showDate' => true, 'showBranch' => true, 'f' => $f, 'branches' => $branches, 'routeName' => 'reports.profit-loss'])

    <div class="section animate-in">
        <h2 class="section-title"><i class="fas fa-calculator"></i> {{ __('Summary') }}</h2>
        <div class="table-wrapper" style="max-width: 420px;">
            <table class="table">
                <tr><td><strong>{{ __('Revenue (Sales)') }}</strong></td><td style="text-align: right;">{{ $currencySymbol ?? 'Rs' }}{{ number_format($summary['revenue'], 2) }}</td></tr>
                <tr><td>{{ __('Cost of goods sold') }}</td><td style="text-align: right;">{{ $currencySymbol ?? 'Rs' }}{{ number_format($summary['cogs'], 2) }}</td></tr>
                <tr><td><strong>{{ __('Gross profit') }}</strong></td><td style="text-align: right;">{{ $currencySymbol ?? 'Rs' }}{{ number_format($summary['gross_profit'], 2) }}</td></tr>
                <tr><td>{{ __('Operating expenses') }}</td><td style="text-align: right;">{{ $currencySymbol ?? 'Rs' }}{{ number_format($summary['expenses'], 2) }}</td></tr>
                <tr><td><strong>{{ __('Net profit / (Loss)') }}</strong></td><td style="text-align: right;">{{ $currencySymbol ?? 'Rs' }}{{ number_format($summary['net_profit'], 2) }}</td></tr>
            </table>
        </div>
    </div>

    <div class="section animate-in">
        <h2 class="section-title"><i class="fas fa-receipt"></i> {{ __('Expenses in period') }}</h2>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('Amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        <tr>
                            <td>{{ $row->expense_date->format('M d, Y') }}</td>
                            <td>{{ __(array_merge(\App\Models\CompanyOtherExpense::businessExpenseCategories(), \App\Models\CompanyOtherExpense::ownerDrawingsCategory())[$row->category] ?? $row->category) }}</td>
                            <td>{{ $row->description }}</td>
                            <td>{{ $currencySymbol ?? 'Rs' }}{{ number_format($row->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align: center; color: var(--gray-500); padding: 24px;">{{ __('No expenses in this period.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
