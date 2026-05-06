<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('restaurant_orders', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('restaurant_tables', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('restaurant_orders', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('restaurant_tables', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
