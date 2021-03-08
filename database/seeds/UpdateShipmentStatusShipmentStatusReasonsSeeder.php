<?php

use Illuminate\Database\Seeder;

class UpdateShipmentStatusShipmentStatusReasonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reason_ids = [26, 29, 43, 44, 51, 2, 22, 55, 56, 16, 58, 38, 9, 10, 11, 20, 57, 48];

        DB::table('shipment_status_shipment_status_reason')->insert(array(
            array('shipment_status_id' => 8, 'shipment_status_reason_id' => 60),
            array('shipment_status_id' => 18, 'shipment_status_reason_id' => 61),
        ));

        DB::table('shipment_status_shipment_status_reason')->whereIn('shipment_status_reason_id', $reason_ids)->delete();
    }
}
