<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateShipmentReasonUnlinkForConsigneeNotIntrested extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status_shipment_status_reason')->where('shipment_status_id', 12)->where('shipment_status_reason_id',22)->delete();
    }
}
