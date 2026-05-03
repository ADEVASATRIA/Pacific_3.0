<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambahkan compound index untuk mempercepat query halaman ticket_view:
     *
     * ticket_types:
     *   - idx_tt_active_type_weight : filter is_active + tipe_khusus, sort by weight
     *     (SoftDeletes otomatis menambahkan WHERE deleted_at IS NULL di query)
     *
     * package_combos:
     *   - idx_pc_active_weight      : filter is_active, sort by weight
     */
    public function up(): void
    {
        Schema::table('ticket_types', function (Blueprint $table) {
            $table->index(
                ['is_active', 'tipe_khusus', 'weight'],
                'idx_tt_active_type_weight'
            );
        });

        Schema::table('package_combos', function (Blueprint $table) {
            $table->index(
                ['is_active', 'weight'],
                'idx_pc_active_weight'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_types', function (Blueprint $table) {
            $table->dropIndex('idx_tt_active_type_weight');
        });

        Schema::table('package_combos', function (Blueprint $table) {
            $table->dropIndex('idx_pc_active_weight');
        });
    }
};
