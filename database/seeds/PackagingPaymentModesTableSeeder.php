<?php

use Illuminate\Database\Seeder;

class PackagingPaymentModesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('packaging_payment_modes')->truncate();
        DB::table('packaging_payment_modes')->insert(array(
            array('id'=>1,'mode'=>'Cash On Delivery'),
            array('id'=>2,'mode'=>'Adjust In Payment'),
        ));
    }
}
