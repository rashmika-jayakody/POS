<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('batch_number', 100);
            $table->decimal('quantity', 12, 3)->default(0);
            $table->date('received_at');
            $table->date('expiry_date')->nullable();
            $table->unsignedBigInteger('grn_item_id')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'branch_id', 'received_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_batches');
    }
};
