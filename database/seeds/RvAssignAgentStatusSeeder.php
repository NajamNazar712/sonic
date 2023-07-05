<?php

use Illuminate\Database\Seeder;

class RvAssignAgentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rv_assign_agent_statuses')->truncate();

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('rv_assign_agent_statuses')->insert(array(
            array('shipment_status_id'=> 20,'name' => 'Return', 'call_finding_id'=> NULL, 'created_at' => $timestamp, 'updated_at'=>$timestamp ),
            array('shipment_status_id'=> 13,'name' => 'Re-Attempt', 'call_finding_id'=> NULL, 'created_at' => $timestamp, 'updated_at'=>$timestamp ),
            array('shipment_status_id'=> 54,'name' => 'Intercept', 'call_finding_id'=> NULL, 'created_at' => $timestamp, 'updated_at'=>$timestamp ),
            array('shipment_status_id'=> 9,'name' => 'On Hold', 'call_finding_id'=> NULL, 'created_at' => $timestamp, 'updated_at'=>$timestamp ),
            array('shipment_status_id'=> NULL,'name' => 'Unresponsive', 'call_finding_id'=> 1, 'created_at' => $timestamp, 'updated_at'=>$timestamp ),
        ));
    }
}
