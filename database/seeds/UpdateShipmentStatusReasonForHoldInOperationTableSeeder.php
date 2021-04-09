<?php

use Illuminate\Database\Seeder;

class UpdateShipmentStatusReasonForHoldInOperationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status_reason')->insert(array(
            array('id' => 62, 'name' => 'On Hold In Operations'),
        ));

        DB::table('shipment_status_shipment_status_reason')->insert(array(
            array('shipment_status_id' => 24, 'shipment_status_reason_id' => 62),
        ));
    }
}
