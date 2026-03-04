<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        // Show all roles except system_owner (reserved for platform)
        $roles = Role::where('name', '!=', 'system_owner')
            ->with('permissions')
            ->orderBy('name')
            ->get();

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required', 'string', 'max:255', 'regex:/^[a-z0-9_]+$/',
                'unique:roles,name',
                'not_in:system_owner',
            ],
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ], [
            'name.regex' => 'Role name must contain only lowercase letters, numbers, and underscores.',
            'name.unique' => 'A role with this name already exists.',
            'name.not_in' => 'The name "system_owner" is reserved.',
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
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
