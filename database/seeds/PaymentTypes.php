<?php

use Illuminate\Database\Seeder;

class PaymentTypes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('trax_payment_names')->insert(
            [
            ['name' => 'Delivery'],
            ['name' => 'Invoice'],
            ]
        );
    }
}
