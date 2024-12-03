<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'AYA Bank',
                'image' => 'aya_banking.png',
            ],
            [
                'name' => 'AYA Pay',
                'image' => 'aya_pay.png',
            ],
            [
                'name' => 'CB Bank',
                'image' => 'cb_banking.png',
            ],
            [
                'name' => 'CB Pay',
                'image' => 'cb_pay.png',
            ],
            [
                'name' => 'KBZ Bank',
                'image' => 'kbz_banking.png',
            ],
            [
                'name' => 'KBZ Pay',
                'image' => 'kpay.png',
            ],
            [
                'name' => 'MAB Bank',
                'image' => 'mab_banking.png',
            ],
            [
                'name' => 'UAB Pay',
                'image' => 'uab_pay.png',
            ],
            [
                'name' => 'WAVE Pay',
                'image' => 'wave.png',
            ],
            [
                'name' => 'YOMA Bank',
                'image' => 'yoma_banking.png',
            ],
        ];

        DB::table('payment_types')->insert($types);
    }
}
