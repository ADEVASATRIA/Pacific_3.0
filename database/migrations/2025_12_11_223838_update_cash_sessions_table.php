<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_sessions', function (Blueprint $table) {
            // Hapus kolom lama
            if (Schema::hasColumn('cash_sessions', 'penjualan_fnb')) {
                $table->dropColumn('penjualan_fnb');
            }

            // Tambah kolom baru
            $table->decimal('penjualan_fnb_kolam', 15, 2)->nullable()->after('saldo_akhir');
            $table->decimal('penjualan_fnb_cafe', 15, 2)->nullable()->after('penjualan_fnb_kolam');
        });
    }

    public function down(): void
    {
        Schema::table('cash_sessions', function (Blueprint $table) {
            // rollback perubahan
            $table->decimal('penjualan_fnb', 15, 2)->nullable()->after('saldo_akhir');

            $table->dropColumn('penjualan_fnb_kolam');
            $table->dropColumn('penjualan_fnb_cafe');
        });
    }
};
