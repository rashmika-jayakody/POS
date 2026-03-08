<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('restaurant_orders')) {
            return;
        }
        Schema::create('restaurant_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('restaurant_table_id')->nullable()->constrained('restaurant_tables')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Server/waiter
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->string('order_no', 60)->unique()->index(); // e.g., "ORD-20260305-0001"
            $table->enum('order_type', ['dine_in', 'takeout', 'delivery'])->default('dine_in');
            $table->enum('status', ['pending', 'confirmed', 'preparing', 'ready', 'served', 'completed', 'cancelled'])->default('pending');
            $table->integer('guest_count')->nullable();
            $table->text('special_instructions')->nullable();
            $table->text('dietary_preferences')->nullable();
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount_total', 14, 2)->default(0);
            $table->decimal('tax_total', 14, 2)->default(0);
            $table->decimal('service_charge', 14, 2)->default(0);
            $table->decimal('grand_total', 14, 2)->default(0);
            $table->boolean('is_split')->default(false);
            $table->integer('split_count')->default(1); // Number of splits
            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('preparing_at')->nullable();
            $table->dateTime('ready_at')->nullable();
            $table->dateTime('served_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['tenant_id', 'branch_id', 'status']);
            $table->index(['restaurant_table_id', 'status']);
            $table->index('order_no');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_orders');
    }
};
