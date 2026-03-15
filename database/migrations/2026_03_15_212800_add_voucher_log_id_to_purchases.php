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
            //
            $table->unsignedBigInteger('voucher_log_id')
                    ->nullable()
                    ->after('voucher_id');
            $table->foreign('voucher_log_id')->references('id')->on('voucher_log');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            //
            $table->dropForeign(['voucher_log_id']);
            $table->dropColumn('voucher_log_id');
        });
    }
};
