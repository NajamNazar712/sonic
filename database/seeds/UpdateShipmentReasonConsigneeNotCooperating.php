<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateShipmentReasonConsigneeNotCooperating extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Http\Models\ShipmentStatusReason::where('id', 21)->delete();
        DB::table('shipment_status_shipment_status_reason')->where('shipment_status_reason_id', 21)->delete();
    }
}
