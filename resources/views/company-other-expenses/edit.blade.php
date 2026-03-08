@extends('layouts.admin')

@section('title', __('Edit Other Expense'))

@section('content')
    <div class="page-header animate-in" style="max-width: 800px; margin: 0 auto 28px auto;">
        <div class="page-title">
            <i class="fas fa-edit"></i>
            {{ __('Edit Other Expense: :description', ['description' => $expense->description]) }}
        </div>
        <div class="page-subtitle">{{ __('Update category, amount, date, or notes.') }}</div>
    </div>

    <div class="section animate-in" style="max-width: 800px; margin: 0 auto;">
        <form action="{{ route('company-other-expenses.update', $expense) }}" method="POST">
            @csrf
            @method('PUT')
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Category') }}</label>
                    <select name="category" required
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                        <optgroup label="{{ __('— Business expenses (reduce profit, in P&L) —') }}">
                            @foreach(\App\Models\CompanyOtherExpense::businessExpenseCategories() as $value => $label)
                                <option value="{{ $value }}" {{ old('category', $expense->category) == $value ? 'selected' : '' }}>{{ __($label) }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="{{ __('— Owner (reduces cash only, not in P&L) —') }}">
                            @foreach(\App\Models\CompanyOtherExpense::ownerDrawingsCategory() as $value => $label)
                                <option value="{{ $value }}" {{ old('category', $expense->category) == $value ? 'selected' : '' }}>{{ __($label) }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                    @error('category') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Expense Date') }}</label>
                    <input type="date" name="expense_date" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('expense_date') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div style="grid-column: span 2;">
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Description') }}</label>
                    <input type="text" name="description" value="{{ old('description', $expense->description) }}" required
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('description') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Amount') }} ({{ $currencySymbol ?? 'Rs' }})</label>
                    <input type="number" name="amount" value="{{ old('amount', $expense->amount) }}" required min="0" step="0.01"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('amount') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Location (optional)') }}</label>
                    <select name="branch_id"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                        <option value="">{{ __('— All / Head office —') }}</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id', $expense->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    @error('branch_id') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div style="grid-column: span 2;">
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">{{ __('Notes (optional)') }}</label>
                    <textarea name="notes" rows="3"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit; resize: none;">{{ old('notes', $expense->notes) }}</textarea>
                    @error('notes') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid var(--gray-100); padding-top: 20px;">
                <a href="{{ route('company-other-expenses.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> {{ __('Update Expense') }}</button>
            </div>
        </form>
    </div>
@endsection
