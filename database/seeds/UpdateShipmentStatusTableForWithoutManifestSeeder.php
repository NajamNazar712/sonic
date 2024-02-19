<?php

use Illuminate\Database\Seeder;

class UpdateShipmentStatusTableForWithoutManifestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status')->insert(array(
            array('id' => 68, 'code' => 'S-MM','name' => 'Shipment - Misrouted (New)','description' => 'Shipment - Misrouted (New)'),
            array('id' => 67, 'code' => 'S-WM','name' => 'Shipment - Without Menifest','description' => 'Shipment - Without Menifest'),
        ));
    }
}
