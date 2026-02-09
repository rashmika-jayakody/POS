<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'create product',
            'edit product',
            'delete product',
            'view product',
            'create sale',
            'view sale',
            'create invoice',
            'view invoice',
            'view reports',
            'schedule maintenance',
            'view maintenance'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $business_owner = Role::firstOrCreate(['name' => 'business_owner']);
        $business_owner->givePermissionTo(Permission::all());

        $cashier = Role::firstOrCreate(['name' => 'cashier']);
        $cashier->givePermissionTo(['create sale', 'view sale', 'create invoice', 'view invoice']);

        $branch_admin = Role::firstOrCreate(['name' => 'branch_admin']);
        $branch_admin->givePermissionTo(['view reports', 'schedule maintenance', 'view maintenance', 'view product']);

        $system_owner = Role::firstOrCreate(['name' => 'system_owner']);
        $system_owner->givePermissionTo(Permission::all());
    }

}
