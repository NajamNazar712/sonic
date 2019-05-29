<?php

use Illuminate\Database\Seeder;

class UpdateShipmentStatusReasonForNonServiceAreaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('shipment_status_reason')->insert(array(
            array('id' => 34, 'name' => ''),

        ));
        DB::table('shipment_status_shipment_status_reason')->insert(array(
            array('shipment_status_id' => 56, 'shipment_status_reason_id' => 31),
            array('shipment_status_id' => 56, 'shipment_status_reason_id' => 32),
            array('shipment_status_id' => 56, 'shipment_status_reason_id' => 33),
        ));
    }
}
