<?php

use Illuminate\Database\Seeder;

class UpdateV2PickupRequestLegendSeederForReversePickup extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('v2_pickup_request_legends')->insert(array(
            array('id' => 8, 'name' => 'Reverse Pickup', 'color' => '#BFEFE2'),

        ));
    }
}
