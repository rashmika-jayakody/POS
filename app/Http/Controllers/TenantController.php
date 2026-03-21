<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        // Only system_owner should reach here (middleware check in routes)
        $tenants = Tenant::withCount(['branches', 'users'])->get();
        return view('tenants.index', compact('tenants'));
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['branches', 'users' => fn ($q) => $q->with(['branch', 'roles'])]);
        return view('tenants.show', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'status' => 'nullable|in:active,suspended',
            'plan' => 'nullable|string|in:' . implode(',', array_keys(config('plans', []))),
        ]);

        $tenant->update($validated);

        return back()->with('success', 'Shop status updated successfully.');
    }

    public function destroy(Tenant $tenant)
    {
        // Handle deletion if necessary, but usually we just suspend for safety
        $tenant->update(['status' => 'suspended']);
        return redirect()->route('tenants.index')->with('success', 'Shop has been suspended.');
    }
}
