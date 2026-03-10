<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentMethodTypesTable extends Migration
{
    public function up()
    {
        Schema::create('payment_method_types', function (Blueprint $table) {

            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_method_types');
    }
}
