<?php

use Illuminate\Database\Seeder;

class AddShipmentStatusReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status_reason')->insert(array(
            array('id' => 77, 'name' => 'Replacement item not matched'),
        ));
        DB::table('shipment_status_shipment_status_reason')->insert(array(
            array('shipment_status_id' => 56, 'shipment_status_reason_id' => 77),
        ));
    }
}
