<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('no_ktp', 20)->nullable()->after('phone');
            $table->string('sertifikat_pelatih')->nullable()->after('no_ktp');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['no_ktp', 'sertifikat_pelatih']);
        });
    }
};
