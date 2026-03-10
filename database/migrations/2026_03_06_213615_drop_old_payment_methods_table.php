<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class DropOldPaymentMethodsTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('payment_methods');
    }

    public function down()
    {
        //
    }
}
