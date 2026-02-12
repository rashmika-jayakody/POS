<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FoundationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Tenant
        $tenant = \App\Models\Tenant::create([
            'name' => 'Super Grocers PVT LTD',
            'email' => 'contact@supergrocers.com',
            'phone' => '0112233445',
            'address' => '123 Market Street, City Center',
        ]);

        // 2. Create Branches
        $mainBranch = \App\Models\Branch::create([
            'tenant_id' => $tenant->id,
            'name' => 'Main Branch',
            'address' => '123 Market Street, City Center',
            'phone' => '0112233445',
        ]);

        $downtownBranch = \App\Models\Branch::create([
            'tenant_id' => $tenant->id,
            'name' => 'Downtown Branch',
            'address' => '456 Business Blvd, Downtown',
            'phone' => '0115566778',
        ]);

        // 3. Create Users and assign roles
        // Business Owner
        $owner = \App\Models\User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Business Owner (Tenant Admin)',
            'email' => 'owner@supergrocers.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        ]);
        $owner->assignRole('business_owner');

        // Cashier for Main Branch
        $cashier = \App\Models\User::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $mainBranch->id,
            'name' => 'Jane (Cashier)',
            'email' => 'cashier1@supergrocers.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        ]);
        $cashier->assignRole('cashier');

        echo "Foundation seeded successfully.\n";
    }
}
