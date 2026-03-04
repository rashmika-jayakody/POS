<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('grn_items')) {
            return;
        }

        Schema::table('grn_items', function (Blueprint $table) {
            if (! Schema::hasColumn('grn_items', 'batch_number')) {
                $table->string('batch_number', 100)->nullable()->after('subtotal');
            }
            if (! Schema::hasColumn('grn_items', 'expiry_date')) {
                $table->date('expiry_date')->nullable()->after('batch_number');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('grn_items')) {
            return;
        }

        Schema::table('grn_items', function (Blueprint $table) {
            $table->dropColumn(['batch_number', 'expiry_date']);
        });
    }
};
