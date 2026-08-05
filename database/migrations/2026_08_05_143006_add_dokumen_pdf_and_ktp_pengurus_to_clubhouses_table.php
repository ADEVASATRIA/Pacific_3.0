<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clubhouses', function (Blueprint $table) {
            $table->string('dokumen_pdf')->nullable()->after('phone');
            $table->string('ktp_pengurus')->nullable()->after('dokumen_pdf');
        });
    }

    public function down(): void
    {
        Schema::table('clubhouses', function (Blueprint $table) {
            $table->dropColumn(['dokumen_pdf', 'ktp_pengurus']);
        });
    }
};
