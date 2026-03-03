@extends('layouts.admin')

@section('title', 'Add Other Expense')

@section('content')
    <div class="page-header animate-in" style="max-width: 800px; margin: 0 auto 28px auto;">
        <div class="page-title">
            <i class="fas fa-plus-circle"></i>
            Add Company Other Expense
        </div>
        <div class="page-subtitle">Business expenses (utilities, rent, salary, etc.) or owner withdrawals (drawings).</div>
    </div>

    <div class="section animate-in" style="max-width: 800px; margin: 0 auto;">
        <form action="{{ route('company-other-expenses.store') }}" method="POST">
            @csrf
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Category</label>
                    <select name="category" required id="expenseCategory"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                        <optgroup label="— Business expenses (reduce profit, in P&L) —">
                            @foreach(\App\Models\CompanyOtherExpense::businessExpenseCategories() as $value => $label)
                                <option value="{{ $value }}" {{ old('category') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="— Owner (reduces cash only, not in P&L) —">
                            @foreach(\App\Models\CompanyOtherExpense::ownerDrawingsCategory() as $value => $label)
                                <option value="{{ $value }}" {{ old('category') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                    <p style="font-size: 0.75rem; color: var(--gray-500); margin-top: 6px;">Don’t mix business expenses with owner drawings.</p>
                    @error('category') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Expense Date</label>
                    <input type="date" name="expense_date" value="{{ old('expense_date', now()->format('Y-m-d')) }}" required
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('expense_date') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div style="grid-column: span 2;">
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Description</label>
                    <input type="text" name="description" value="{{ old('description') }}" required placeholder="e.g. Monthly rent, Electricity bill"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('description') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Amount ({{ $currencySymbol ?? 'Rs' }})</label>
                    <input type="number" name="amount" value="{{ old('amount') }}" required min="0" step="0.01" placeholder="0.00"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                    @error('amount') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Location (optional)</label>
                    <select name="branch_id"
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit;">
                        <option value="">— All / Head office —</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    @error('branch_id') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
                <div style="grid-column: span 2;">
                    <label style="display: block; font-weight: 700; color: var(--navy-dark); margin-bottom: 8px;">Notes (optional)</label>
                    <textarea name="notes" rows="3" placeholder="Any additional details..."
                        style="width: 100%; padding: 12px; border: 1px solid var(--gray-300); border-radius: 8px; font-family: inherit; resize: none;">{{ old('notes') }}</textarea>
                    @error('notes') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid var(--gray-100); padding-top: 20px;">
                <a href="{{ route('company-other-expenses.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Record Expense</button>
            </div>
        </form>
    </div>
@endsection
