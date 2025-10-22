<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;

class FixSwappedCustomerFields extends Command
{
    protected $signature = 'fix:swapped-customers {--confirm-fix : Lakukan perbaikan data langsung tanpa konfirmasi}';
    protected $description = 'Memperbaiki data customer yang kolom phone dan name tertukar (dengan preview dan konfirmasi)';

    public function handle()
    {
        $this->info("🔍 Mengecek data customer yang kolom phone & name kemungkinan tertukar...\n");

        $suspects = [];
        $customers = Customer::select('id', 'name', 'phone')->get();

        foreach ($customers as $c) {
            $isPhoneNotNumeric = preg_match('/[a-zA-Z]/', $c->phone);
            $isNameNumeric = preg_match('/^\d{8,}$/', $c->name);

            if ($isPhoneNotNumeric && $isNameNumeric) {
                $suspects[] = $c;
            }
        }

        if (empty($suspects)) {
            $this->info("✅ Tidak ditemukan data yang tertukar.");
            return Command::SUCCESS;
        }

        // 🔸 Tampilkan kandidat data tertukar
        $this->warn("⚠️ Ditemukan " . count($suspects) . " data yang kemungkinan tertukar:\n");

        foreach ($suspects as $c) {
            $this->line(" - ID {$c->id} | name='{$c->name}' | phone='{$c->phone}'");
        }

        // 🔸 Jika tidak ada opsi --confirm-fix, minta konfirmasi dulu
        if (!$this->option('confirm-fix')) {
            $confirm = $this->confirm("\nApakah kamu ingin memperbaiki semua data ini sekarang?", false);
            if (!$confirm) {
                $this->info("❎ Perbaikan dibatalkan. Tidak ada data yang diubah.");
                return Command::SUCCESS;
            }
        }

        // 🔹 Jalankan perbaikan
        $fixed = 0;
        foreach ($suspects as $c) {
            $oldPhone = $c->phone;
            $oldName = $c->name;

            $c->phone = $oldName;
            $c->name = $oldPhone;
            $c->save();

            $this->info("✅ Fixed ID {$c->id}: phone='{$c->phone}' | name='{$c->name}'");
            $fixed++;
        }

        $this->info("\n🎯 Total data diperbaiki: {$fixed}");
        return 0; // untuk hilangkan warning Symfony
    }
}
