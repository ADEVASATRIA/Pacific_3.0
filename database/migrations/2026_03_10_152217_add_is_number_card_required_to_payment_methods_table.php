<?php

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
        Schema::table('payment_methods', function (Blueprint $table) {
            // Menambahkan kolom boolean dengan default 'false' (0)
            $table->boolean('is_number_card_required')->default(false)->after('is_approval_code_required');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            // Menghapus kolom jika migration di-rollback
            $table->dropColumn('is_number_card_required');
        });
    }
};