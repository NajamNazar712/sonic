<?php

use Illuminate\Database\Seeder;

class UpdateShipmentStatusReasonForLostTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status_reason')->insert(array(
            array('id' => 36, 'name' => 'Snatched by consignee'),
            array('id' => 37, 'name' => 'Misplaced by rider')
        ));
        DB::table('shipment_status_shipment_status_reason')->insert(array(
            array('shipment_status_id' => 18, 'shipment_status_reason_id' => 36),
            array('shipment_status_id' => 18, 'shipment_status_reason_id' => 37)
        ));
    }
}
