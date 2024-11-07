<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateShipmentStatusReasonForShipmentLostRequestedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status_reason')->insert(array(
            array('name' => 'Shipment Lost - Requested'),
        ));

        $id = DB::table('shipment_status_reason')->where('name', '=','Shipment Lost - Requested')->first()->id;

        DB::table('shipment_status_shipment_status_reason')->insert(array(
            array('shipment_status_id' => 18, 'shipment_status_reason_id' => $id),
        ));
    }
}
