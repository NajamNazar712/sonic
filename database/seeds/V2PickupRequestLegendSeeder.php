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
            array('id' => 1, 'name' => 'New Pickup', 'color' => '#00FFFF'),
            array('id' => 2, 'name' => 'Vendor Pickup', 'color' => '#FFFF00'),
            array('id' => 3, 'name' => 'Pickup Request Having Try & Buy Shipment', 'color' => '#FFA500'),
            array('id' => 4, 'name' => 'Pickup Request Not Picked on 1st Attempt', 'color' => '#D8BFD8'),
            array('id' => 5, 'name' => 'Pickup Request Not Picked on 2nd Attempt', 'color' => '#FFC0CB'),
            array('id' => 6, 'name' => 'Pickup Request Not Picked on more than 2 Attempt', 'color' => '#FA8072'),
            array('id' => 7, 'name' => 'Pickup Request Created After cut-off-time', 'color' => '#FF0000'),

        ));
    }
}
