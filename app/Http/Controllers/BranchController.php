<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        // Multi-tenancy handled by BelongsToTenant trait global scope
        $branches = Branch::withCount('users')->get();
        return view('branches.index', compact('branches'));
    }

    public function create()
    {
        return view('branches.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        Branch::create($validated);

        return redirect()->route('branches.index')->with('success', 'Location created successfully.');
    }

    public function edit(Branch $branch)
    {
        // Multi-tenancy check (redundant but safe)
        if ($branch->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
        return view('branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        if ($branch->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $branch->update($validated);

        return redirect()->route('branches.index')->with('success', 'Location updated successfully.');
    }

    public function destroy(Branch $branch)
    {
        if ($branch->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        if ($branch->users()->count() > 0) {
            return back()->with('error', 'Cannot delete location with assigned staff.');
        }

        $branch->delete();
        return redirect()->route('branches.index')->with('success', 'Location deleted successfully.');
    }
}
