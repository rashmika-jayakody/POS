<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = config('plans', []);

        foreach ($plans as $slug => $details) {
            \App\Models\Plan::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $details['name'],
                    'description' => $details['description'],
                    'price_lkr' => $details['price_lkr'],
                    'max_branches' => $details['max_branches'] ?? -1,
                    'max_users' => $details['max_users'] ?? -1,
                    'features' => $details['features'] ?? [],
                    'is_active' => true,
                ]
            );
        }
    }
}
