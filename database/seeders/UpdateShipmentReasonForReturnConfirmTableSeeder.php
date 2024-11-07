<?php

use Illuminate\Database\Seeder;

class UpdateShipmentReasonForReturnConfirmTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status_reason')->insert(array(
            array('id' => 38, 'name' => 'Open Parcel'),
            array('id' => 39, 'name' => 'Delay in Delivery'),
        ));
        DB::table('shipment_status_shipment_status_reason')->insert(array(
            array('shipment_status_id' => 20, 'shipment_status_reason_id' => 2),
            array('shipment_status_id' => 20, 'shipment_status_reason_id' => 5),
            array('shipment_status_id' => 20, 'shipment_status_reason_id' => 9),
            array('shipment_status_id' => 20, 'shipment_status_reason_id' => 38),
			array('shipment_status_id' => 20, 'shipment_status_reason_id' => 39),
        ));
    }
}
