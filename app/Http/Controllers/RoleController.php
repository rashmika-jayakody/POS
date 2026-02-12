<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        // We only show roles relevant to a business owner
        $roles = Role::whereIn('name', ['business_owner', 'branch_manager', 'cashier'])
            ->with('permissions')
            ->get();

        return view('roles.index', compact('roles'));
    }

    public function edit(Role $role)
    {
        // Protect system_owner role if it happens to be passed
        if ($role->name === 'system_owner') {
            abort(403);
        }

        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'system_owner') {
            abort(403);
        }

        $request->validate([
            'permissions' => 'array',
        ]);

        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('roles.index')->with('success', 'Role permissions updated successfully.');
    }
}
