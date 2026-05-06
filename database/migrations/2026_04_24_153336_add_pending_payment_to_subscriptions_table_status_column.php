<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE subscriptions MODIFY COLUMN status ENUM('pending_payment', 'active', 'trialing', 'past_due', 'cancelled', 'expired') NOT NULL DEFAULT 'pending_payment'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE subscriptions MODIFY COLUMN status ENUM('active', 'trialing', 'past_due', 'cancelled', 'expired') NOT NULL DEFAULT 'active'");
    }
};