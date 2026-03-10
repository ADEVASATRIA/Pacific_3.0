<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentMethodsTable extends Migration
{
    public function up()
    {
        Schema::create('payment_methods', function (Blueprint $table) {

            $table->unsignedBigInteger('id'); // mapping legacy id

            $table->string('name');

            $table->unsignedBigInteger('payment_method_type_id');

            $table->string('provider')->nullable();

            $table->string('img_thumbnail')->nullable();

            $table->integer('sort_order')->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->primary('id');

            $table->foreign('payment_method_type_id')
                ->references('id')
                ->on('payment_method_types');

        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_methods');
    }
}
