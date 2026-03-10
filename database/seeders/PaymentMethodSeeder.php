<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    public function run()
    {

        DB::table('payment_methods')->insert([

            [
                'id'=>1,
                'name'=>'Cash',
                'payment_method_type_id'=>1,
                'provider'=>null
            ],

            [
                'id'=>2,
                'name'=>'QRIS BCA',
                'payment_method_type_id'=>2,
                'provider'=>'bca'
            ],

            [
                'id'=>3,
                'name'=>'QRIS Mandiri',
                'payment_method_type_id'=>2,
                'provider'=>'mandiri'
            ],

            [
                'id'=>4,
                'name'=>'Debit BCA',
                'payment_method_type_id'=>3,
                'provider'=>'bca'
            ],

            [
                'id'=>5,
                'name'=>'Debit Mandiri',
                'payment_method_type_id'=>3,
                'provider'=>'mandiri'
            ],

            [
                'id'=>6,
                'name'=>'Transfer Bank',
                'payment_method_type_id'=>4,
                'provider'=>null
            ],

            [
                'id'=>7,
                'name'=>'QRIS BRI',
                'payment_method_type_id'=>2,
                'provider'=>'bri'
            ],

            [
                'id'=>8,
                'name'=>'Debit BRI',
                'payment_method_type_id'=>3,
                'provider'=>'bri'
            ]

        ]);

    }
}
