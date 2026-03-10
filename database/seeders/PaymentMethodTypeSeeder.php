<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodTypeSeeder extends Seeder
{
    public function run()
    {

        DB::table('payment_method_types')->insert([
            ['id'=>1,'name'=>'Cash','slug'=>'cash'],
            ['id'=>2,'name'=>'QRIS','slug'=>'qris'],
            ['id'=>3,'name'=>'Debit Card','slug'=>'debit'],
            ['id'=>4,'name'=>'Bank Transfer','slug'=>'bank_transfer'],
            ['id'=>5,'name'=>'Promo','slug'=>'promo'],
            ['id'=>6,'name'=>'E-Wallet','slug'=>'ewallet'],
            ['id'=>7,'name'=>'Voucher','slug'=>'voucher']
        ]);

    }
}
