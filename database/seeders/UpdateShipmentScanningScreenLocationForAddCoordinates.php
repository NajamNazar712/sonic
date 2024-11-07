<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateShipmentScanningScreenLocationForAddCoordinates extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_scanning_screen_locations')->insert(array(
            array('id' => 15, 'name' => 'Support - Add Coordinates')
        ));
    }
}
