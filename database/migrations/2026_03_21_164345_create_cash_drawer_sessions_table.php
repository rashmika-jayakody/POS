<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_drawer_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('session_number', 60)->unique();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->decimal('opening_balance', 14, 2)->default(0);
            $table->decimal('closing_balance', 14, 2)->nullable();
            $table->decimal('expected_balance', 14, 2)->nullable();
            $table->decimal('cash_sales', 14, 2)->default(0);
            $table->decimal('card_sales', 14, 2)->default(0);
            $table->decimal('other_sales', 14, 2)->default(0);
            $table->decimal('refunds_total', 14, 2)->default(0);
            $table->decimal('cash_added', 14, 2)->default(0);
            $table->decimal('cash_removed', 14, 2)->default(0);
            $table->decimal('variance', 14, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'branch_id', 'status']);
            $table->index(['user_id', 'opened_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_drawer_sessions');
    }
};
