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
        DB::table('shipment_status_shipment_status_reason')->whereIn('shipment_status_reason_id', [21, 24])->delete();
    }
}
