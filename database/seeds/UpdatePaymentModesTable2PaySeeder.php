<?php

use Illuminate\Database\Seeder;

class UpdatePaymentModesTable2PaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('payment_modes')->insert(array(
            array('id' => 4, 'mode' => '2Pay')
        ));
    }
}
