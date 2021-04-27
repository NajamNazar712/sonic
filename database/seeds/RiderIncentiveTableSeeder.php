<?php

use Illuminate\Database\Seeder;

class RiderIncentiveTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('riders_shipment_payment_types')->truncate();

        DB::table('riders_shipment_payment_types')->insert(array(
            array('id' => 1, 'name' => 'COD'),
            array('id' => 2, 'name' => 'Non-COD'),
            array('id' => 3, 'name' => 'Verification'),
        ));

        DB::table('riders_shipment_weight_ranges')->truncate();

        DB::table('riders_shipment_weight_ranges')->insert(array(
            array('id' => 1, 'name' => '1.5 KG or below'),
            array('id' => 2, 'name' => '1.5 KG or above'),
            array('id' => 3, 'name' => 'Envelope'),
            array('id' => 4, 'name' => '5 KG or below'),
            array('id' => 5, 'name' => '5.1 KG or above'),
        ));
    }
}
