<?php

use Illuminate\Database\Seeder;

class UpdatePaymentModeForCCDTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_modes')->insert(array(
            array('id' => 5, 'mode' => 'Credit Card on Delivery-CCD')
        ));
    }
}
