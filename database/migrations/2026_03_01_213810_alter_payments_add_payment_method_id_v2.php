<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_method_id')
                ->nullable()
                ->after('payment');
        });

        // Copy data lama (biarkan NULL tetap NULL)
        DB::statement('UPDATE purchases SET payment_method_id = payment WHERE payment IS NOT NULL');

        Schema::table('purchases', function (Blueprint $table) {
            $table->foreign('payment_method_id')
                ->references('id')
                ->on('payment_methods')
                ->nullOnDelete()   // lebih cocok karena nullable
                ->cascadeOnUpdate();
        });
    }

    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign(['payment_method_id']);
            $table->dropColumn('payment_method_id');
        });
    }
};
