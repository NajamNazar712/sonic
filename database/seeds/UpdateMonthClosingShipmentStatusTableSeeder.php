<?php

use Illuminate\Database\Seeder;

class UpdateMonthClosingShipmentStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status')->insert(array(
            array('id' => 51, 'code' => 'S-MC', 'name' => 'Shipment - Month Closing', 'description' => 'The status of a shipment which is pending at a certain state for a month and awaiting action from shipper or station, updated to close the case for further action.')
        ));
    }
}
