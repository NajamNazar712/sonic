<?php

use Illuminate\Database\Seeder;

class UnableToReturnStatusShipmentStatusReasonTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status_reason')->insert(array(
            array('id' => 79, 'name' => 'Negative Balance'),
            array('id' => 80, 'name' => 'Shipment Damage'),
            array('id' => 81, 'name' => 'AWB Change'),
            array('id' => 82, 'name' => 'Flyer Change'),
            array('id' => 83, 'name' => 'Flyer Empty'),
            array('id' => 84, 'name' => 'Short Contents'),
            array('id' => 85, 'name' => 'Content Change'),
            array('id' => 86, 'name' => 'Shipper Address Issue'),
        ));

        DB::table('shipment_status_shipment_status_reason')->insert(array(
            array('shipment_status_id' => 60, 'shipment_status_reason_id' => 79),
            array('shipment_status_id' => 60, 'shipment_status_reason_id' => 80),
            array('shipment_status_id' => 60, 'shipment_status_reason_id' => 81),
            array('shipment_status_id' => 60, 'shipment_status_reason_id' => 82),
            array('shipment_status_id' => 60, 'shipment_status_reason_id' => 83),
            array('shipment_status_id' => 60, 'shipment_status_reason_id' => 84),
            array('shipment_status_id' => 60, 'shipment_status_reason_id' => 85),
            array('shipment_status_id' => 60, 'shipment_status_reason_id' => 86),
        ));
    }
}
