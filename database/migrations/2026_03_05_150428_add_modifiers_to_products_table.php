<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('has_modifiers')->default(false)->after('is_active');
            $table->text('modifier_groups')->nullable()->after('has_modifiers'); // JSON array of modifier group names
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['has_modifiers', 'modifier_groups']);
        });
    }
};
