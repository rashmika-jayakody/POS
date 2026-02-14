<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        $isSystemOwner = auth()->user()->hasRole('system_owner');
        $users = $isSystemOwner
            ? User::with(['branch', 'roles', 'tenant'])->orderBy('tenant_id')->orderBy('name')->get()
            : User::with(['branch', 'roles'])->where('tenant_id', auth()->user()->tenant_id)->get();
        return view('users.index', compact('users', 'isSystemOwner'));
    }

    public function create()
    {
        $tenants = auth()->user()->hasRole('system_owner')
            ? Tenant::orderBy('name')->get()
            : null;
        $branches = Branch::all();
        $roles = Role::whereIn('name', ['business_owner', 'branch_manager', 'cashier'])->get();
        return view('users.create', compact('branches', 'roles', 'tenants'));
    }

    public function store(Request $request)
    {
        $isSystemOwner = auth()->user()->hasRole('system_owner');
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'role' => ['required', 'exists:roles,name'],
        ];
        if ($isSystemOwner) {
            $rules['tenant_id'] = ['required', 'exists:tenants,id'];
        }
        $request->validate($rules);

        $tenantId = $isSystemOwner ? $request->tenant_id : auth()->user()->tenant_id;
        if ($request->branch_id && $tenantId) {
            $branch = Branch::withoutGlobalScope('tenant')->where('id', $request->branch_id)->where('tenant_id', $tenantId)->first();
            if (!$branch) {
                return back()->withErrors(['branch_id' => 'Selected branch does not belong to the selected shop.'])->withInput();
            }
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tenant_id' => $tenantId,
            'branch_id' => $request->branch_id,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        if (!auth()->user()->hasRole('system_owner') && $user->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $tenants = auth()->user()->hasRole('system_owner') ? Tenant::orderBy('name')->get() : null;
        $branches = Branch::withoutGlobalScope('tenant')->where('tenant_id', $user->tenant_id)->get();
        $roles = Role::whereIn('name', ['business_owner', 'branch_manager', 'cashier'])->get();
        return view('users.edit', compact('user', 'branches', 'roles', 'tenants'));
    }

    public function update(Request $request, User $user)
    {
        if (!auth()->user()->hasRole('system_owner') && $user->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'branch_id' => $request->branch_id,
            'is_active' => $request->has('is_active'),
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            abort(403, 'You cannot delete your own account.');
        }
        if (!auth()->user()->hasRole('system_owner') && $user->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
