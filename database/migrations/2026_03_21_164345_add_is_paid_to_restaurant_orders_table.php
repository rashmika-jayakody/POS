<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurant_orders', function (Blueprint $table) {
            $table->boolean('is_paid')->default(false)->after('grand_total');
            $table->string('payment_method', 40)->nullable()->after('is_paid');
            $table->timestamp('paid_at')->nullable()->after('payment_method');
            $table->decimal('tip_amount', 14, 2)->default(0)->after('paid_at');
            $table->enum('tip_type', ['fixed', 'percentage'])->nullable()->after('tip_amount');
        });
    }

    public function down(): void
    {
        Schema::table('restaurant_orders', function (Blueprint $table) {
            $table->dropColumn(['is_paid', 'payment_method', 'paid_at', 'tip_amount', 'tip_type']);
        });
    }
};
