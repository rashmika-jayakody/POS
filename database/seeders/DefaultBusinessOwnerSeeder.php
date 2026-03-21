<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultBusinessOwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::first();
        $branch = Branch::where('tenant_id', $tenant->id)->first();

        $user = User::updateOrCreate(
            ['email' => 'owner@poshere.lk'],
            [
                'name' => 'Default Business Owner',
                'password' => Hash::make('Test@123'),
                'tenant_id' => $tenant->id,
                'branch_id' => $branch->id,
                'is_active' => true,
            ]
        );

        $user->assignRole('business_owner');
    }
}
