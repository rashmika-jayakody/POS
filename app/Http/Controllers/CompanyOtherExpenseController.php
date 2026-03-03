<?php

namespace App\Http\Controllers;

use App\Models\CompanyOtherExpense;
use App\Models\Branch;
use Illuminate\Http\Request;

class CompanyOtherExpenseController extends Controller
{
    public function index()
    {
        $expenses = CompanyOtherExpense::with('branch')
            ->orderByDesc('expense_date')
            ->orderByDesc('created_at')
            ->paginate(20);

        $totalBusinessExpenses = CompanyOtherExpense::businessExpenses()->sum('amount');
        $totalOwnerDrawings = CompanyOtherExpense::ownerDrawings()->sum('amount');

        return view('company-other-expenses.index', compact('expenses', 'totalBusinessExpenses', 'totalOwnerDrawings'));
    }

    public function create()
    {
        $branches = Branch::orderBy('name')->get();
        return view('company-other-expenses.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|in:' . implode(',', CompanyOtherExpense::allCategoryKeys()),
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'branch_id' => 'nullable|exists:branches,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        CompanyOtherExpense::create($validated);

        return redirect()->route('company-other-expenses.index')
            ->with('success', 'Other expense recorded successfully.');
    }

    public function edit(CompanyOtherExpense $companyOtherExpense)
    {
        $branches = Branch::orderBy('name')->get();
        return view('company-other-expenses.edit', [
            'expense' => $companyOtherExpense,
            'branches' => $branches,
        ]);
    }

    public function update(Request $request, CompanyOtherExpense $companyOtherExpense)
    {
        $validated = $request->validate([
            'category' => 'required|string|in:' . implode(',', CompanyOtherExpense::allCategoryKeys()),
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'branch_id' => 'nullable|exists:branches,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $companyOtherExpense->update($validated);

        return redirect()->route('company-other-expenses.index')
            ->with('success', 'Other expense updated successfully.');
    }

    public function destroy(CompanyOtherExpense $companyOtherExpense)
    {
        $companyOtherExpense->delete();
        return redirect()->route('company-other-expenses.index')
            ->with('success', 'Other expense deleted successfully.');
    }
}
