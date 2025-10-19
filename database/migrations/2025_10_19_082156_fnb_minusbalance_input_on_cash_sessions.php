<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cash_sessions', function (Blueprint $table) {
            // Menambahkan kolom fnb_balance (saldo F&B yang sebenarnya)
            $table->decimal('fnb_balance', 15, 2)->default(0)->after('penjualan_fnb');

            // Menambahkan kolom minus_balance (saldo minus atau kekurangan)
            $table->decimal('minus_balance', 15, 2)->default(0)->after('fnb_balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_sessions', function (Blueprint $table) {
            $table->dropColumn(['fnb_balance', 'minus_balance']);
        });
    }
};
