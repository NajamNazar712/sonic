<?php

use Illuminate\Database\Seeder;

class UpdatePaymentModeTableAddPrepaidSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_modes')->insert(array(
            array('id' => 4, 'mode'=>'Prepaid')
        ));
    }
}
