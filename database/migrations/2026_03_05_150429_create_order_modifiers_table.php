<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_modifiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_item_id')->nullable()->constrained('restaurant_order_items')->onDelete('cascade');
            $table->string('modifier_type', 50); // e.g., "addon", "substitution", "instruction"
            $table->string('name', 200); // e.g., "Extra Cheese", "No Onions", "Well Done"
            $table->decimal('price_adjustment', 14, 2)->default(0); // Can be positive or negative
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index('restaurant_order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_modifiers');
    }
};
