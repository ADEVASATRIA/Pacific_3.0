<?php

use App\Models\Admin;
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
        Schema::create('cash_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('staff_id');
            $table->decimal('saldo_awal', 15, 2)->default(0);
            $table->decimal('saldo_akhir', 15, 2)->nullable();
            $table->decimal('penjualan_fnb', 15, 2)->nullable();
            $table->timestamp('waktu_buka')->useCurrent();
            $table->timestamp('waktu_tutup')->nullable();
            $table->boolean('status')->default(1); // 1 = aktif, 0 = closed
            $table->timestamps();

            $table->foreign('staff_id')->references('id')->on('admins')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
