<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        // Scope to current tenant (handled by global scope if trait exists, but let's be explicit if not)
        $users = User::with(['branch', 'roles'])->where('tenant_id', auth()->user()->tenant_id)->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $branches = Branch::all();
        $roles = Role::whereIn('name', ['business_owner', 'branch_manager', 'cashier'])->get();
        return view('users.create', compact('branches', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tenant_id' => auth()->user()->tenant_id,
            'branch_id' => $request->branch_id,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        // Security check
        if ($user->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $branches = Branch::all();
        $roles = Role::whereIn('name', ['business_owner', 'branch_manager', 'cashier'])->get();
        return view('users.edit', compact('user', 'branches', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->tenant_id !== auth()->user()->tenant_id) {
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
        if ($user->tenant_id !== auth()->user()->tenant_id || $user->id === auth()->id()) {
            abort(403);
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
