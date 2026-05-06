<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->string('payhere_payment_id')->nullable()->unique();
            $table->decimal('amount', 14, 2);
            $table->string('currency', 10)->default('LKR');
            $table->enum('status', ['pending', 'successful', 'failed', 'refunded'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('plan')->nullable();
            $table->json('payhere_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};