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
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all()); // Admin gets all permissions

        $cashier = Role::firstOrCreate(['name' => 'cashier']);
        $cashier->givePermissionTo(['create sale', 'view sale', 'create invoice', 'view invoice']);

        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->givePermissionTo(['view reports', 'schedule maintenance', 'view maintenance']);

        $superadmin = Role::firstOrCreate(['name' => 'superadmin']);
        $superadmin->givePermissionTo(Permission::all()); // Superadmin gets all permissions
    }
    
}
