<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('restaurant_table_id')->nullable()->constrained('restaurant_tables')->onDelete('set null');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->string('reservation_no', 60)->unique()->index();
            $table->string('customer_name', 200);
            $table->string('customer_phone', 20);
            $table->string('customer_email', 255)->nullable();
            $table->dateTime('reservation_date');
            $table->integer('guest_count');
            $table->enum('status', ['pending', 'confirmed', 'seated', 'completed', 'cancelled', 'no_show'])->default('pending');
            $table->text('special_requests')->nullable();
            $table->text('notes')->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('seated_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['tenant_id', 'branch_id', 'reservation_date']);
            $table->index(['restaurant_table_id', 'reservation_date']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
