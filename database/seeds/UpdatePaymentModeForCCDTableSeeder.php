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
        DB::table('payment_modes')->where('id', 2)->update(['mode' => 'CCD']);
    }
}
