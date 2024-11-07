<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ShipmentScanningScreenLocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_scanning_screen_locations')->truncate();

        DB::table('shipment_scanning_screen_locations')->insert(array(
            array('id' => 1, 'name' => 'Delivery - Arrival of Shipments'),
            array('id' => 2, 'name' => 'Cargo - Create/Update'),
            array('id' => 3, 'name' => 'Cargo - Receive'),
            array('id' => 4, 'name' => 'Delivery - Create'),
            array('id' => 5, 'name' => 'Delivery - Remove Fake Status'),
            array('id' => 6, 'name' => 'Delivery - Log Fake Statuses'),
            array('id' => 7, 'name' => 'Return - Create'),
            array('id' => 8, 'name' => 'Quick Tracking'),
            array('id' => 9, 'name' => 'Tracking'),
            array('id' => 10, 'name' => 'Support - Misroute - Update'),
            array('id' => 11, 'name' => 'Support - Lost - Add Lost Shipments'),
            array('id' => 12, 'name' => 'Support - Replacement To Regular - Collected'),
            array('id' => 13, 'name' => 'Support - Change Shipment Amount'),
            array('id' => 14, 'name' => 'Support - Change Shipment Weight')
        ));
    }
}
