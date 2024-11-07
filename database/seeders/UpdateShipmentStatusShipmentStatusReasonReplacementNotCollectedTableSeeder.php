<?php

use Illuminate\Database\Seeder;

class UpdateShipmentStatusShipmentStatusReasonReplacementNotCollectedTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status_reason')->insert(array(
            array('id' => 31, 'name' => 'Shipper’s mistake in booking'),
            array('id' => 32, 'name' => 'Consignee did not handover the shipment due to product or shipper issue'),
            array('id' => 33, 'name' => 'Consignee wants to receive both parcel and agrees with shipper to pay for both'),
        ));
        DB::table('shipment_status_shipment_status_reason')->insert(array(
            array('shipment_status_id' => 56, 'shipment_status_reason_id' => 31),
            array('shipment_status_id' => 56, 'shipment_status_reason_id' => 32),
            array('shipment_status_id' => 56, 'shipment_status_reason_id' => 33),
        ));
    }
}
