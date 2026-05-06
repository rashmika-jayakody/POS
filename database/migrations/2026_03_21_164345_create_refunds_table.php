<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('refund_number', 60)->unique();
            $table->enum('type', ['sale', 'restaurant_order'])->default('sale');
            $table->unsignedBigInteger('original_sale_id')->nullable();
            $table->unsignedBigInteger('original_order_id')->nullable();
            $table->string('original_invoice_no', 60)->nullable();
            $table->enum('reason', ['damaged', 'wrong_item', 'customer_request', 'quality_issue', 'other']);
            $table->text('reason_notes')->nullable();
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('tax_total', 14, 2)->default(0);
            $table->decimal('grand_total', 14, 2)->default(0);
            $table->string('refund_method', 40)->default('cash');
            $table->boolean('inventory_updated')->default(false);
            $table->foreignId('cash_drawer_session_id')->nullable()->constrained('cash_drawer_sessions')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'branch_id', 'created_at']);
            $table->index(['type', 'original_sale_id']);
            $table->index(['type', 'original_order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
