<?php

use Illuminate\Database\Seeder;

class UpdateShipmentStatusReasonForCnicUnavailable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status_reason')->insert(array(
            array('id' => 63, 'name' => 'Unavailability of CNIC')
        ));
        DB::table('shipment_status_shipment_status_reason')->insert(array(
            array('shipment_status_id' => 8, 'shipment_status_reason_id' => 63)
        ));
    }
}
