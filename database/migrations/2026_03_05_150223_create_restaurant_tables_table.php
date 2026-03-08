<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('restaurant_tables')) {
            return;
        }
        Schema::create('restaurant_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name', 50); // e.g., "Table 1", "Booth 5"
            $table->string('floor_section', 100)->nullable(); // e.g., "Main Dining", "Patio", "VIP"
            $table->integer('capacity')->default(4); // Number of seats
            $table->integer('position_x')->nullable(); // For floor layout visualization
            $table->integer('position_y')->nullable();
            $table->enum('status', ['available', 'occupied', 'reserved', 'cleaning'])->default('available');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['tenant_id', 'branch_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_tables');
    }
};
