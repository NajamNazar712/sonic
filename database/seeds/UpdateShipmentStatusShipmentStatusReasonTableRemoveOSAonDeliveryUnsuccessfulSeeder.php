<?php

use Illuminate\Database\Seeder;

class UpdateShipmentStatusShipmentStatusReasonTableRemoveOSAonDeliveryUnsuccessfulSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status_shipment_status_reason')->delete(array(
            array('shipment_status_id' => 49, 'shipment_status_reason_id' => 12)
        ));
    }
}
