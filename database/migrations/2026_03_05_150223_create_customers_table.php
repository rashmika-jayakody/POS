<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('name', 200);
            $table->string('email', 255)->nullable();
            $table->string('phone', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('address')->nullable();
            $table->text('dietary_preferences')->nullable(); // Allergies, vegetarian, etc.
            $table->text('favorite_items')->nullable(); // JSON array of favorite product IDs
            $table->integer('loyalty_points')->default(0);
            $table->decimal('lifetime_spent', 14, 2)->default(0);
            $table->integer('visit_count')->default(0);
            $table->dateTime('last_visit_at')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['tenant_id', 'email']);
            $table->index(['tenant_id', 'phone']);
            $table->index('loyalty_points');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
