<?php

use Illuminate\Database\Seeder;

class PaymentModeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_modes')->truncate();

        DB::table('payment_modes')->insert(array(
            array('mode'=>'COD'),
            array('mode'=>'Card'),
            array('mode'=>'Mobile')
        ));
    }
}
