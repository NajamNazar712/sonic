<?php

use Illuminate\Database\Seeder;

class UpdateShipmentStatusReaseonDeliveryStoppedTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status_reason')->insert(array(
            array('id' => 35, 'name' => 'Delivery Stopped'),
        ));
        DB::table('shipment_status_shipment_status_reason')->insert(array(
            array('shipment_status_id' => 12, 'shipment_status_reason_id' => 35),
        ));
    }
}
