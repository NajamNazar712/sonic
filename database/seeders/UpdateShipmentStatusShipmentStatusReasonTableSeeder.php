<?php

use Illuminate\Database\Seeder;

class UpdateShipmentStatusShipmentStatusReasonTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status_shipment_status_reason')->insert(array(
            array('shipment_status_id' => 24, 'shipment_status_reason_id' => 6),
            array('shipment_status_id' => 24, 'shipment_status_reason_id' => 30),
            array('shipment_status_id' => 47, 'shipment_status_reason_id' => 14),
            array('shipment_status_id' => 47, 'shipment_status_reason_id' => 23),
            array('shipment_status_id' => 47, 'shipment_status_reason_id' => 24),
            array('shipment_status_id' => 47, 'shipment_status_reason_id' => 25),
            array('shipment_status_id' => 47, 'shipment_status_reason_id' => 29),
            array('shipment_status_id' => 48, 'shipment_status_reason_id' => 18),
            array('shipment_status_id' => 12, 'shipment_status_reason_id' => 12),
        ));
    }
}
