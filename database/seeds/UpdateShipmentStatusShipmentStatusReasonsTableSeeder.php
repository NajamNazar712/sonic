<?php

use Illuminate\Database\Seeder;

class UpdateShipmentStatusShipmentStatusReasonsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status_shipment_status_reason')->insert(array(

            array('shipment_status_id' => 8, 'shipment_status_reason_id' => 55),
            array('shipment_status_id' => 8, 'shipment_status_reason_id' => 56),

            array('shipment_status_id' => 12, 'shipment_status_reason_id' => 57),
            array('shipment_status_id' => 12, 'shipment_status_reason_id' => 55),
            array('shipment_status_id' => 12, 'shipment_status_reason_id' => 56),

            array('shipment_status_id' => 15, 'shipment_status_reason_id' => 58),


        ));
    }
}
