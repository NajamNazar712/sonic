<?php

use Illuminate\Database\Seeder;

class UpdateShipmentStatusShipmentStatusReasonTableForRVStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status_shipment_status_reason')->insert(array(
            array('shipment_status_id' => 8, 'shipment_status_reason_id' => 5),
            array('shipment_status_id' => 8, 'shipment_status_reason_id' => 40),
            array('shipment_status_id' => 8, 'shipment_status_reason_id' => 45),
            array('shipment_status_id' => 12, 'shipment_status_reason_id' => 45),
            array('shipment_status_id' => 12, 'shipment_status_reason_id' => 28),
            array('shipment_status_id' => 12, 'shipment_status_reason_id' => 60),
            array('shipment_status_id' => 12, 'shipment_status_reason_id' => 63),
        ));
    }
}
