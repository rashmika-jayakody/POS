<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('cash_drawer_session_id')
                ->nullable()
                ->after('payment_method')
                ->constrained('cash_drawer_sessions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['cash_drawer_session_id']);
            $table->dropColumn('cash_drawer_session_id');
        });
    }
};
