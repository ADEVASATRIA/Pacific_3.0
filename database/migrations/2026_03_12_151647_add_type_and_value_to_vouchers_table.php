<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->enum('type_voucher', ['fixed', 'percent'])->default('fixed')->after('requirements');
            $table->string('value')->default(0)->after('type_voucher');
        });
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn(['type_voucher', 'value']);
        });
    }
};
