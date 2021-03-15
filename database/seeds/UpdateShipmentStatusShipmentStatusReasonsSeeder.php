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
        $reason_ids = [43, 44, 51, 22, 56, 16, 58, 10, 11, 20, 57];

        DB::table('shipment_status_shipment_status_reason')->insert(array(
            array('shipment_status_id' => 8, 'shipment_status_reason_id' => 60),
            array('shipment_status_id' => 18, 'shipment_status_reason_id' => 61),
        ));

        DB::table('shipment_status_shipment_status_reason')->where('shipment_status_id', 8)->where('shipment_status_reason_id', 5)->delete();
        DB::table('shipment_status_shipment_status_reason')->where('shipment_status_id', 8)->where('shipment_status_reason_id', 45)->delete();
        DB::table('shipment_status_shipment_status_reason')->where('shipment_status_id', 8)->where('shipment_status_reason_id', 2)->delete();
        DB::table('shipment_status_shipment_status_reason')->where('shipment_status_id', 8)->where('shipment_status_reason_id', 55)->delete();
        DB::table('shipment_status_shipment_status_reason')->where('shipment_status_id', 12)->where('shipment_status_reason_id', 2)->delete();
        DB::table('shipment_status_shipment_status_reason')->where('shipment_status_id', 12)->where('shipment_status_reason_id', 9)->delete();
        DB::table('shipment_status_shipment_status_reason')->where('shipment_status_id', 12)->where('shipment_status_reason_id', 55)->delete();
        DB::table('shipment_status_shipment_status_reason')->where('shipment_status_id', 7)->where('shipment_status_reason_id', 26)->delete();
        DB::table('shipment_status_shipment_status_reason')->where('shipment_status_id', 7)->where('shipment_status_reason_id', 29)->delete();
        DB::table('shipment_status_shipment_status_reason')->where('shipment_status_id', 9)->where('shipment_status_reason_id', 45)->delete();
        DB::table('shipment_status_shipment_status_reason')->where('shipment_status_id', 12)->where('shipment_status_reason_id', 13)->delete();
        DB::table('shipment_status_shipment_status_reason')->where('shipment_status_id', 12)->where('shipment_status_reason_id', 42)->delete();

        DB::table('shipment_status_shipment_status_reason')->whereIn('shipment_status_reason_id', $reason_ids)->delete();
    }
}
