<?php

use Illuminate\Database\Seeder;

class UpdateShipmentReasonForShipperRefused extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status_reason')->insert(array(
            array('id' => 64, 'name' => 'Shipper Refused'),
        ));
        DB::table('shipment_status_shipment_status_reason')->insert(array(
            array('shipment_status_id' => 24, 'shipment_status_reason_id' => 64)
        ));
    }
}
