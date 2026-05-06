<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refund_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('refund_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('qty', 12, 3);
            $table->decimal('unit_price', 14, 2);
            $table->decimal('line_total', 14, 2);
            $table->decimal('cost_price_at_refund', 14, 2)->nullable();
            $table->boolean('restocked')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['refund_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refund_items');
    }
};
