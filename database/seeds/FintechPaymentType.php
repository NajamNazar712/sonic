<?php

use Illuminate\Database\Seeder;

class FintechPaymentType extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('fintech_payment_types')->insert(array(
            array('id' => 1, 'payment_types' => 'Card'),
            array('id' => 2, 'payment_types' => 'Account'),
            array('id' => 3, 'payment_types' => 'Wallet'),
        ));
    }
}
