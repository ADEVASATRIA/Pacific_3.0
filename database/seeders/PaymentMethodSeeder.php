<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    public function run()
    {
        DB::table('payment_methods')->insertOrIgnore([
            [
                'id' => 1,
                'name' => 'Cash',
                // 'img_thumbnail' => 'payments/cash.png',
                'type' => 'cash',
                'provider' => null,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'QRIS BCA',
                // 'img_thumbnail' => 'payments/qris_bca.png',
                'type' => 'qris',
                'provider' => 'BCA',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'QRIS Mandiri',
                // 'img_thumbnail' => 'payments/qris_mandiri.png',
                'type' => 'qris',
                'provider' => 'Mandiri',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Debit BCA',
                // 'img_thumbnail' => 'payments/debit_bca.png',
                'type' => 'debit',
                'provider' => 'BCA',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Debit Mandiri',
                // 'img_thumbnail' => 'payments/debit_mandiri.png',
                'type' => 'debit',
                'provider' => 'Mandiri',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'name' => 'Transfer',
                // 'img_thumbnail' => 'payments/transfer.png',
                'type' => 'bank_transfer',
                'provider' => null,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'name' => 'QRIS BRI',
                // 'img_thumbnail' => 'payments/qris_bri.png',
                'type' => 'qris',
                'provider' => 'BRI',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'name' => 'Debit BRI',
                // 'img_thumbnail' => 'payments/debit_bri.png',
                'type' => 'debit',
                'provider' => 'BRI',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}