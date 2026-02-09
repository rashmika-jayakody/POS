<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = \App\Models\Tenant::where('name', 'Super Grocers PVT LTD')->first();
        if (!$tenant) {
            echo "Tenant not found. Run FoundationSeeder first.\n";
            return;
        }

        $branches = $tenant->branches;

        // 1. Create Units
        $kg = \App\Models\Unit::create(['tenant_id' => $tenant->id, 'name' => 'Kilogram', 'short_code' => 'kg']);
        $pcs = \App\Models\Unit::create(['tenant_id' => $tenant->id, 'name' => 'Pieces', 'short_code' => 'pcs']);
        $ltr = \App\Models\Unit::create(['tenant_id' => $tenant->id, 'name' => 'Liters', 'short_code' => 'ltr']);

        // 2. Create Categories
        $vegetables = \App\Models\Category::create(['tenant_id' => $tenant->id, 'name' => 'Vegetables', 'slug' => 'vegetables']);
        $fruits = \App\Models\Category::create(['tenant_id' => $tenant->id, 'name' => 'Fruits', 'slug' => 'fruits']);
        $dairy = \App\Models\Category::create(['tenant_id' => $tenant->id, 'name' => 'Dairy', 'slug' => 'dairy']);

        // 3. Create Products and Stock for each branch
        $products = [
            [
                'category_id' => $vegetables->id,
                'unit_id' => $kg->id,
                'name' => 'Carrots',
                'barcode' => 'VEG001',
                'cost_price' => 80.00,
                'selling_price' => 120.00,
            ],
            [
                'category_id' => $fruits->id,
                'unit_id' => $kg->id,
                'name' => 'Red Apples',
                'barcode' => 'FRT001',
                'cost_price' => 250.00,
                'selling_price' => 380.00,
            ],
            [
                'category_id' => $dairy->id,
                'unit_id' => $ltr->id,
                'name' => 'Fresh Milk',
                'barcode' => 'DRY001',
                'cost_price' => 180.00,
                'selling_price' => 220.00,
            ],
        ];

        foreach ($products as $pData) {
            $product = \App\Models\Product::create(array_merge($pData, ['tenant_id' => $tenant->id]));

            // Seed stock for each branch
            foreach ($branches as $branch) {
                \App\Models\Stock::create([
                    'tenant_id' => $tenant->id,
                    'product_id' => $product->id,
                    'branch_id' => $branch->id,
                    'quantity' => rand(50, 200),
                    'low_stock_threshold' => 15,
                ]);
            }
        }

        echo "Inventory seeded successfully.\n";
    }
}
