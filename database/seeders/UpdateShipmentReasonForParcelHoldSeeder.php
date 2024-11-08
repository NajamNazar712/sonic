<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateShipmentReasonForParcelHoldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status_reason')->insert(array(
            array('id' => 43, 'name' => 'Hold in Operation due to Saturday Closed'),
            array('id' => 44, 'name' => 'Not attempted due to restricted area'),
        ));
        DB::table('shipment_status_shipment_status_reason')->insert(array(
            array('shipment_status_id' => 7, 'shipment_status_reason_id' => 43),
            array('shipment_status_id' => 7, 'shipment_status_reason_id' => 44),
        ));
    }
}
