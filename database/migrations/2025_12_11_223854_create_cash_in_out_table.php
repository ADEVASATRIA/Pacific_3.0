<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_in_out', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cash_session_id');

            $table->decimal('nominal_uang', 15, 2)->default(0);
            $table->integer('type')->comment('1 = cash_in, 2 = cash_out');
            $table->longText('keterangan')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('cash_session_id')
                ->references('id')
                ->on('cash_sessions')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_in_out');
    }
};
