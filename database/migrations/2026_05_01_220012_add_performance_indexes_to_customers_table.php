<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambahkan index untuk mempercepat query contact book modal:
     * - idx_customers_search : composite index untuk filter deleted_at + sort nama
     * - idx_customers_phone  : index untuk pencarian & auto-fill by phone
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Composite index: filter baris aktif (deleted_at IS NULL) + sort by name
            $table->index(['deleted_at', 'name'], 'idx_customers_search');

            // Single index: digunakan oleh searchByPhone & LIKE phone search
            $table->index('phone', 'idx_customers_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('idx_customers_search');
            $table->dropIndex('idx_customers_phone');
        });
    }
};
