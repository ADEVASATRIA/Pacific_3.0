<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ticket_types', function (Blueprint $table) {
            Schema::table('ticket_types', function (Blueprint $table) {
                $table->unsignedTinyInteger('tipe_khusus')
                    ->default(1)
                    ->comment('1: Tiket Normal, 2: Tiket Pengantar, 3: Tiket Pelatih, 4: Tiket Member, 5: Tiket Member Club, 6: Tiket Add-Ons')
                    ->change();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_types', function (Blueprint $table) {
            Schema::table('ticket_types', function (Blueprint $table) {
                $table->unsignedTinyInteger('tipe_khusus')
                    ->default(1)
                    ->comment('1:tiket normal, 2:tiket pengantar, 3:tiket pelatih, 4:member')
                    ->change();
            });
        });
    }
};
