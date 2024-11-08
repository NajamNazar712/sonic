<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class botCallRvAssignAndSubStatus extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        // DB::table('rv_assign_agent_statuses')->insert(array(
        //     array('id' => 9, 'shipment_status_id' => NULL, 'name' => 'Shipment is another status', 'shipment_status_name' => 'Shipment is Another Status', 'call_finding_id' => NULL, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'is_visible' => 1),
        // ));
        // DB::table('rv_assign_agent_sub_statuses')->insert(array(
        //     array('id' => 38, 'name' => 'Manual Entry','shipment_status_reason_id'=> Null,'rv_assign_agent_status_id'=> 9,'is_active'=> 1,'created_at' => $timestamp, 'updated_at' => $timestamp),
        //     array('id' => 39, 'name' => 'Congestion','shipment_status_reason_id'=> Null,'rv_assign_agent_status_id'=> 6,'is_active'=> 1,'created_at' => $timestamp, 'updated_at' => $timestamp),
        // ));
        DB::table('rv_assign_agent_sub_statuses')
        ->where('id', 33)
            ->update([
                'name' => 'Unresponsive',
            ]);
    }
}
