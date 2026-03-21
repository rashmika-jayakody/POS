<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Unit;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockBatch;
use App\Models\Tenant;
use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();
        if (!$tenant) return;

        $branch = Branch::where('tenant_id', $tenant->id)->first();
        if (!$branch) return;

        // 1. Units
        $units = [
            ['name' => 'Kilogram', 'short_code' => 'kg'],
            ['name' => 'Pieces', 'short_code' => 'pcs'],
            ['name' => 'Bottle', 'short_code' => 'bottle'],
            ['name' => 'Bundle', 'short_code' => 'bundle'],
        ];

        foreach ($units as $u) {
            Unit::updateOrCreate(
                ['tenant_id' => $tenant->id, 'short_code' => $u['short_code']],
                ['name' => $u['name']]
            );
        }

        // 2. Categories
        $categories = [
            ['name' => 'Grocery', 'description' => 'Essential grocery items'],
            ['name' => 'Beverages', 'description' => 'Drinks and beverages'],
            ['name' => 'Snacks', 'description' => 'Packaged snacks and sweets'],
            ['name' => 'Vegetables', 'description' => 'Fresh vegetables'],
        ];

        foreach ($categories as $c) {
            Category::updateOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => Str::slug($c['name'])],
                ['name' => $c['name'], 'description' => $c['description'], 'is_active' => true]
            );
        }

        // 3. Products
        $kgUnit = Unit::where('tenant_id', $tenant->id)->where('short_code', 'kg')->first();
        $pcsUnit = Unit::where('tenant_id', $tenant->id)->where('short_code', 'pcs')->first();
        $bottleUnit = Unit::where('tenant_id', $tenant->id)->where('short_code', 'bottle')->first();

        $groceryCat = Category::where('tenant_id', $tenant->id)->where('slug', 'grocery')->first();
        $beverageCat = Category::where('tenant_id', $tenant->id)->where('slug', 'beverages')->first();
        $snackCat = Category::where('tenant_id', $tenant->id)->where('slug', 'snacks')->first();
        $vegCat = Category::where('tenant_id', $tenant->id)->where('slug', 'vegetables')->first();

        $products = [
            [
                'name' => 'Red Rice',
                'category_id' => $groceryCat->id,
                'unit_id' => $kgUnit->id,
                'code' => 'GRO001',
                'cost_price' => 180,
                'selling_price' => 220,
            ],
            [
                'name' => 'White Sugar',
                'category_id' => $groceryCat->id,
                'unit_id' => $kgUnit->id,
                'code' => 'GRO002',
                'cost_price' => 240,
                'selling_price' => 275,
            ],
            [
                'name' => 'Coca Cola 500ml',
                'category_id' => $beverageCat->id,
                'unit_id' => $bottleUnit->id,
                'code' => 'BEV001',
                'cost_price' => 120,
                'selling_price' => 150,
            ],
            [
                'name' => 'Munchee Cream Cracker',
                'category_id' => $snackCat->id,
                'unit_id' => $pcsUnit->id,
                'code' => 'SNA001',
                'cost_price' => 110,
                'selling_price' => 135,
            ],
            [
                'name' => 'Carrot',
                'category_id' => $vegCat->id,
                'unit_id' => $kgUnit->id,
                'code' => 'VEG001',
                'cost_price' => 300,
                'selling_price' => 380,
            ],
        ];

        $fifo = app(\App\Services\FifoStockService::class);

        foreach ($products as $p) {
            $product = Product::updateOrCreate(
                ['tenant_id' => $tenant->id, 'code' => $p['code']],
                [
                    'category_id' => $p['category_id'],
                    'unit_id' => $p['unit_id'],
                    'name' => $p['name'],
                    'cost_price' => $p['cost_price'],
                    'selling_price' => $p['selling_price'],
                    'is_active' => true,
                ]
            );

            // 4. Initial Stock (using FIFO batches)
            // Clear existing stock to avoid duplicates when re-seeding
            StockBatch::where('product_id', $product->id)->where('branch_id', $branch->id)->delete();
            Stock::where('product_id', $product->id)->where('branch_id', $branch->id)->delete();
            
            $fifo->addBatch(
                $tenant->id,
                $product->id,
                $branch->id,
                100, // quantity
                'BATCH-' . $product->code . '-001', // batch number
                null, // expiry
                null, // grn item id
                $p['cost_price'] // purchase price
            );
        }
    }
}
