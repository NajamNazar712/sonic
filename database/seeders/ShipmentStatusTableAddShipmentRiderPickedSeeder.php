<?php

use Illuminate\Database\Seeder;

class ShipmentStatusTableAddShipmentRiderPickedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status')->insert(array(
            array('id' => 53, 'code' => 'S-RP', 'name' => 'Shipment - Rider Picked', 'description' => 'Shipment  has been picked by Rider')
        ));
    }
}
