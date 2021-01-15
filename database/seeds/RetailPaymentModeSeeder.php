<?php

use Illuminate\Database\Seeder;

class RetailPaymentModeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('retail_payment_modes')->truncate();

        DB::table('retail_payment_modes')->insert(array(
            array('id' => 1, 'name' => 'Cash'),
            array('id' => 2, 'name' => 'Credit'),
            array('id' => 3, 'name' => 'QR')
        ));
    }
}
