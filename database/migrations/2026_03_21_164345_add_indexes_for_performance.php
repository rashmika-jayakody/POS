<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            $this->addIndexesMysql();
        } else {
            $this->addIndexesGeneric();
        }
    }

    protected function addIndexesMysql(): void
    {
        $indexes = [
            'sales' => [
                'sales_tenant_branch_date_idx' => ['tenant_id', 'branch_id', 'sale_date'],
                'sales_invoice_no_index' => ['invoice_no'],
            ],
            'sale_items' => [
                'sale_items_sale_product_idx' => ['sale_id', 'product_id'],
            ],
            'products' => [
                'products_tenant_category_active_idx' => ['tenant_id', 'category_id', 'is_active'],
                'products_barcode_index' => ['barcode'],
            ],
            'stocks' => [
                'stocks_tenant_branch_product_idx' => ['tenant_id', 'branch_id', 'product_id'],
            ],
            'stock_batches' => [
                'stock_batches_tenant_branch_product_idx' => ['tenant_id', 'branch_id', 'product_id'],
                'stock_batches_expiry_date_index' => ['expiry_date'],
            ],
            'restaurant_orders' => [
                'restaurant_orders_tenant_branch_status_idx' => ['tenant_id', 'branch_id', 'status', 'created_at'],
            ],
            'restaurant_order_items' => [
                'restaurant_order_items_order_product_idx' => ['restaurant_order_id', 'product_id'],
            ],
            'customers' => [
                'customers_tenant_active_idx' => ['tenant_id', 'is_active'],
            ],
            'activity_logs' => [
                'activity_logs_tenant_type_date_idx' => ['tenant_id', 'log_type', 'created_at'],
            ],
            'grns' => [
                'grns_tenant_branch_status_idx' => ['tenant_id', 'branch_id', 'status'],
            ],
            'company_other_expenses' => [
                'expenses_tenant_branch_date_idx' => ['tenant_id', 'branch_id', 'expense_date'],
            ],
        ];

        foreach ($indexes as $table => $tableIndexes) {
            $existingIndexes = $this->getExistingIndexes($table);

            foreach ($tableIndexes as $indexName => $columns) {
                if (! in_array($indexName, $existingIndexes)) {
                    try {
                        DB::statement("ALTER TABLE `{$table}` ADD INDEX `{$indexName}` (`".implode('`, `', $columns).'`)');
                    } catch (\Exception $e) {
                        // Index might already exist, skip
                    }
                }
            }
        }
    }

    protected function getExistingIndexes(string $table): array
    {
        $database = DB::connection()->getDatabaseName();
        $results = DB::select('SELECT INDEX_NAME as index_name FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? GROUP BY INDEX_NAME', [$database, $table]);

        return array_map(function ($row) {
            return $row->index_name;
        }, $results);
    }

    protected function addIndexesGeneric(): void
    {
        // Generic fallback for non-MySQL databases
        Schema::table('sales', function (Blueprint $table) {
            $table->index(['tenant_id', 'branch_id', 'sale_date'], 'sales_tenant_branch_date_idx');
            $table->index('invoice_no');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->index(['sale_id', 'product_id'], 'sale_items_sale_product_idx');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index(['tenant_id', 'category_id', 'is_active'], 'products_tenant_category_active_idx');
            $table->index('barcode');
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->index(['tenant_id', 'branch_id', 'product_id'], 'stocks_tenant_branch_product_idx');
        });

        Schema::table('stock_batches', function (Blueprint $table) {
            $table->index(['tenant_id', 'branch_id', 'product_id'], 'stock_batches_tenant_branch_product_idx');
            $table->index('expiry_date');
        });

        Schema::table('restaurant_orders', function (Blueprint $table) {
            $table->index(['tenant_id', 'branch_id', 'status', 'created_at'], 'restaurant_orders_tenant_branch_status_idx');
        });

        Schema::table('restaurant_order_items', function (Blueprint $table) {
            $table->index(['restaurant_order_id', 'product_id'], 'restaurant_order_items_order_product_idx');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->index(['tenant_id', 'is_active'], 'customers_tenant_active_idx');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index(['tenant_id', 'log_type', 'created_at'], 'activity_logs_tenant_type_date_idx');
        });

        Schema::table('grns', function (Blueprint $table) {
            $table->index(['tenant_id', 'branch_id', 'status'], 'grns_tenant_branch_status_idx');
        });

        Schema::table('company_other_expenses', function (Blueprint $table) {
            $table->index(['tenant_id', 'branch_id', 'expense_date'], 'expenses_tenant_branch_date_idx');
        });
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            $this->removeIndexesMysql();
        } else {
            $this->removeIndexesGeneric();
        }
    }

    protected function removeIndexesMysql(): void
    {
        $indexes = [
            'sales' => ['sales_tenant_branch_date_idx', 'sales_invoice_no_index'],
            'sale_items' => ['sale_items_sale_product_idx'],
            'products' => ['products_tenant_category_active_idx', 'products_barcode_index'],
            'stocks' => ['stocks_tenant_branch_product_idx'],
            'stock_batches' => ['stock_batches_tenant_branch_product_idx', 'stock_batches_expiry_date_index'],
            'restaurant_orders' => ['restaurant_orders_tenant_branch_status_idx'],
            'restaurant_order_items' => ['restaurant_order_items_order_product_idx'],
            'customers' => ['customers_tenant_active_idx'],
            'activity_logs' => ['activity_logs_tenant_type_date_idx'],
            'grns' => ['grns_tenant_branch_status_idx'],
            'company_other_expenses' => ['expenses_tenant_branch_date_idx'],
        ];

        foreach ($indexes as $table => $tableIndexes) {
            $existingIndexes = $this->getExistingIndexes($table);

            foreach ($tableIndexes as $indexName) {
                if (in_array($indexName, $existingIndexes)) {
                    try {
                        DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$indexName}`");
                    } catch (\Exception $e) {
                        // Index might not exist, skip
                    }
                }
            }
        }
    }

    protected function removeIndexesGeneric(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex('sales_tenant_branch_date_idx');
            $table->dropIndex(['invoice_no']);
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropIndex('sale_items_sale_product_idx');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_tenant_category_active_idx');
            $table->dropIndex(['barcode']);
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->dropIndex('stocks_tenant_branch_product_idx');
        });

        Schema::table('stock_batches', function (Blueprint $table) {
            $table->dropIndex('stock_batches_tenant_branch_product_idx');
            $table->dropIndex(['expiry_date']);
        });

        Schema::table('restaurant_orders', function (Blueprint $table) {
            $table->dropIndex('restaurant_orders_tenant_branch_status_idx');
        });

        Schema::table('restaurant_order_items', function (Blueprint $table) {
            $table->dropIndex('restaurant_order_items_order_product_idx');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('customers_tenant_active_idx');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex('activity_logs_tenant_type_date_idx');
        });

        Schema::table('grns', function (Blueprint $table) {
            $table->dropIndex('grns_tenant_branch_status_idx');
        });

        Schema::table('company_other_expenses', function (Blueprint $table) {
            $table->dropIndex('expenses_tenant_branch_date_idx');
        });
    }
};
