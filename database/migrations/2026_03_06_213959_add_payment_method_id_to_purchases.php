<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentMethodIdToPurchases extends Migration
{
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {

            $table->unsignedBigInteger('payment_method_id')
                ->nullable()
                ->after('payment');

            $table->foreign('payment_method_id')
                ->references('id')
                ->on('payment_methods');

        });
    }

    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {

            $table->dropForeign(['payment_method_id']);
            $table->dropColumn('payment_method_id');

        });
    }
}
