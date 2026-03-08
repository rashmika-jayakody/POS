<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('qty', 12, 3);
            $table->decimal('unit_price', 14, 2);
            $table->decimal('modifier_total', 14, 2)->default(0); // Total from modifiers
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('line_total', 14, 2);
            $table->text('special_instructions')->nullable();
            $table->enum('status', ['pending', 'preparing', 'ready', 'served'])->default('pending');
            $table->timestamps();
            
            $table->index(['restaurant_order_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_order_items');
    }
};
