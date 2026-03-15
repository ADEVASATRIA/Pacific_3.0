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
        Schema::table('purchases', function (Blueprint $table) {
            // Add voucher_id column
            $table->unsignedBigInteger('voucher_id')
                ->nullable()
                ->after('promo_id');

            $table->foreign('voucher_id')
                ->references('id')
                ->on('vouchers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['voucher_id']);

            // Drop column
            $table->dropColumn('voucher_id');
        });
    }
};
