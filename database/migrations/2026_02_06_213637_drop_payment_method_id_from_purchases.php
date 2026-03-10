<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropPaymentMethodIdFromPurchases extends Migration
{
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {

            if (Schema::hasColumn('purchases', 'payment_method_id')) {

                $table->dropForeign(['payment_method_id']);

                $table->dropColumn('payment_method_id');

            }

        });
    }

    public function down()
    {
        //
    }
}
