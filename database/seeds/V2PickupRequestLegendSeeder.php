<?php

use Illuminate\Database\Seeder;

class V2PickupRequestLegendSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('v2_pickup_request_legends')->truncate();
        DB::table('v2_pickup_request_legends')->insert(array(
            array('id' => 1, 'name' => 'New Pickup', 'color' => '#000'),
            array('id' => 2, 'name' => 'Vendor Pickup', 'color' => '#000'),
            array('id' => 3, 'name' => 'Pickup Request Having Try & Buy Shipment', 'color' => '#000'),
            array('id' => 4, 'name' => 'Pickup Request Not Picked on 1st Attempt', 'color' => '#000'),
            array('id' => 5, 'name' => 'Pickup Request Not Picked on 2nd Attempt', 'color' => '#000'),
            array('id' => 6, 'name' => 'Pickup Request Not Picked on more than 2 Attempt', 'color' => '#000'),
            array('id' => 7, 'name' => 'Pickup Request Created After cut-off-time', 'color' => '#000'),
            array('id' => 8, 'name' => 'Late Attempted', 'color' => '#000'),
            array('id' => 9, 'name' => 'Accident/Snatching', 'color' => '#000')
        ));
    }
}
