<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class removeReasonValidationShipmentStatusReasonId extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        \DB::table('shipment_status_shipment_status_reason')->where('shipment_status_id', 12)->where('shipment_status_reason_id', 35)->delete();
    }
}
