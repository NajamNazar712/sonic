<?php

use Illuminate\Database\Seeder;

class UpdateShipmentStatusForShipmentReceivedFromShipperTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status')->insert(array(
            array('id' => 64, 'code' => 'S-RFS', 'name' => 'Shipment - Received From Shipper', 'description' => 'Shipment is received from shipper')
        ));
    }
}
