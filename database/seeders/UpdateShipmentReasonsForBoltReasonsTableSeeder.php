<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateShipmentReasonsForBoltReasonsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status_reason')->insert(array(
            array('id' => 45, 'name' => 'No one came for Self-Collection')
        ));
        DB::table('shipment_status_shipment_status_reason')->insert(array(
            array('shipment_status_id' => 8, 'shipment_status_reason_id' => 45),
            array('shipment_status_id' => 9, 'shipment_status_reason_id' => 45),
            array('shipment_status_id' => 15, 'shipment_status_reason_id' => 45),
            array('shipment_status_id' => 9, 'shipment_status_reason_id' => 17),
            array('shipment_status_id' => 9, 'shipment_status_reason_id' => 18),
        ));
    }
}
