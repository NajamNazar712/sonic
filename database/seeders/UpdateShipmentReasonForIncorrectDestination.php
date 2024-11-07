<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateShipmentReasonForIncorrectDestination extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status_reason')->insert(array(
            array('id' => 51, 'name' => 'Rider Unable to attempt')
        ));
        DB::table('shipment_status_shipment_status_reason')->insert(array(
            array('shipment_status_id' => 7, 'shipment_status_reason_id' => 26),
            array('shipment_status_id' => 7, 'shipment_status_reason_id' => 51)
        ));
    }
}
