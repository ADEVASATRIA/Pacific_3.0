<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentMethodsTable extends Migration
{
    public function up()
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->unsignedBigInteger('id'); // manual id (mapping legacy)
            $table->string('name');
            $table->string('img_thumbnail')->nullable(); // path image
            $table->enum('type', ['cash', 'qris', 'debit', 'bank_transfer', 'promo']);
            $table->string('provider')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->primary('id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_methods');
    }
}
