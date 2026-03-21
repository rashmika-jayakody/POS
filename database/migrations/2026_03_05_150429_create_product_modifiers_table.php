<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_modifiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('name', 200); // e.g., "Extra Cheese", "No Onions"
            $table->string('modifier_group', 100)->nullable(); // e.g., "Toppings", "Cooking Style"
            $table->enum('type', ['addon', 'substitution', 'instruction'])->default('addon');
            $table->decimal('price_adjustment', 14, 2)->default(0); // Can be positive or negative
            $table->boolean('is_required')->default(false); // Must select one from group
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['product_id', 'is_active']);
            $table->index('modifier_group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_modifiers');
    }
};
