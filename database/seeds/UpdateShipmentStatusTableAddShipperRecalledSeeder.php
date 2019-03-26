<?php

use Illuminate\Database\Seeder;

class UpdateShipmentStatusTableAddShipperRecalledSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status')->insert(array(
            array('id' => 50, 'code' => 'S-RE', 'name' => 'Shipment - Recalled', 'description' => 'Shipment was recalled by the shipper for return')
        ));
    }
}
