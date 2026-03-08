@extends('layouts.admin')

@section('title', __('Company Other Expenses'))

@section('content')
    <div class="page-header animate-in">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <div class="page-title">
                    <i class="fas fa-receipt"></i>
                    {{ __('Expenses') }}
                </div>
                <div class="page-subtitle">{{ __('Business expenses (utilities, rent, salary, etc.) and owner withdrawals (drawings). Never mixed in reports.') }}</div>
            </div>
            <a href="{{ route('company-other-expenses.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                {{ __('Add Expense / Drawing') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div
            style="background: rgba(16, 185, 129, 0.1); color: var(--success); padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; border: 1px solid rgba(16, 185, 129, 0.2);">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="stats-grid" style="margin-bottom: 24px;">
        <div class="section animate-in" style="margin-bottom: 0;">
            <div class="section-title"><i class="fas fa-briefcase"></i> {{ __('Business Expenses') }}</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--navy-dark);">
                {{ $currencySymbol ?? 'Rs' }}{{ number_format($totalBusinessExpenses, 2) }}
            </div>
            <p style="font-size: 0.8rem; color: var(--gray-500); margin-top: 8px;">{{ __('Reduces profit · Included in P&L') }}</p>
        </div>
        <div class="section animate-in" style="margin-bottom: 0;">
            <div class="section-title"><i class="fas fa-user"></i> {{ __('Owner Drawings') }}</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--navy-dark);">
                {{ $currencySymbol ?? 'Rs' }}{{ number_format($totalOwnerDrawings, 2) }}
            </div>
            <p style="font-size: 0.8rem; color: var(--gray-500); margin-top: 8px;">{{ __('Reduces cash only · Not in P&L') }}</p>
        </div>
    </div>

    <p style="font-size: 0.85rem; color: var(--gray-500); margin-bottom: 16px;">
        <i class="fas fa-info-circle"></i> {{ __('Sales − Cost − Business expenses = Net profit. Owner drawings are recorded but not counted as expenses.') }}
    </p>

    <div class="section animate-in">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('Location') }}</th>
                        <th>{{ __('Amount') }}</th>
                        <th style="text-align: right;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr>
                            <td>{{ $expense->expense_date->format('M d, Y') }}</td>
                            <td>
                                @if($expense->isOwnerDrawing())
                                    <span class="status-badge inactive" style="background: rgba(148, 163, 184, 0.15); color: var(--gray-600);">
                                        <i class="fas fa-user" style="margin-right: 4px;"></i> {{ __('Drawing') }}
                                    </span>
                                @else
                                    <span class="status-badge active" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">
                                        <i class="fas fa-briefcase" style="margin-right: 4px;"></i> {{ __('Expense') }}
                                    </span>
                                @endif
                            </td>
                            <td><span style="font-weight: 600; color: var(--navy-dark);">{{ __(array_merge(\App\Models\CompanyOtherExpense::businessExpenseCategories(), \App\Models\CompanyOtherExpense::ownerDrawingsCategory())[$expense->category] ?? $expense->category) }}</span></td>
                            <td>{{ $expense->description }}</td>
                            <td>{{ $expense->branch?->name ?? '—' }}</td>
                            <td style="font-weight: 700; color: var(--danger);">{{ $currencySymbol ?? 'Rs' }}{{ number_format($expense->amount, 2) }}</td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <a href="{{ route('company-other-expenses.edit', $expense) }}" class="btn btn-secondary"
                                        style="padding: 6px 10px; font-size: 0.75rem;">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('company-other-expenses.destroy', $expense) }}" method="POST"
                                        onsubmit="return confirm('{{ $expense->isOwnerDrawing() ? __("Delete this drawing?") : __("Delete this expense?") }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn"
                                            style="padding: 6px 10px; font-size: 0.75rem; background: rgba(255, 107, 130, 0.1); color: var(--accent-coral); border: 1px solid rgba(255, 107, 130, 0.2);">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 32px; color: var(--gray-500);">
                                {{ __('No expenses or drawings recorded yet.') }} <a href="{{ route('company-other-expenses.create') }}" style="color: var(--light-blue); font-weight: 600;">{{ __('Add one') }}</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($expenses->hasPages())
            <div style="margin-top: 16px; display: flex; justify-content: center;">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>
@endsection
