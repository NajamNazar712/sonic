<?php

use Illuminate\Database\Seeder;

class RvAssignAgentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return voidp
     */
    public function run()
    {
        DB::table('rv_assign_agent_statuses')->truncate();

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('rv_assign_agent_statuses')->insert(array(
            array('id'=> 1, 'shipment_status_id'=> 20,'name' => 'Return', 'shipment_status_name' => 'Return - Confirm', 'call_finding_id'=> NULL, 'created_at' => $timestamp, 'updated_at'=>$timestamp, 'is_visible'=> 1 ),
            array('id'=> 2, 'shipment_status_id'=> 13,'name' => 'Re-Attempt', 'shipment_status_name' => 'Shipment - Re-Attempt','call_finding_id'=> NULL, 'created_at' => $timestamp, 'updated_at'=>$timestamp, 'is_visible'=> 1 ),
            array('id'=> 3, 'shipment_status_id'=> 54,'name' => 'Intercept', 'shipment_status_name' => 'Intercept Requested','call_finding_id'=> NULL, 'created_at' => $timestamp, 'updated_at'=>$timestamp, 'is_visible'=> 1 ),
            array('id'=> 4, 'shipment_status_id'=> 55,'name' => 'Intercept Approved', 'shipment_status_name' => 'Intercept Approved','call_finding_id'=> NULL, 'created_at' => $timestamp, 'updated_at'=>$timestamp, 'is_visible'=> 0 ),
            array('id'=> 5, 'shipment_status_id'=> 15,'name' => 'On Hold', 'shipment_status_name' => 'Shipment - On Hold for Self Collection','call_finding_id'=> NULL, 'created_at' => $timestamp, 'updated_at'=>$timestamp, 'is_visible'=> 1 ),
            array('id'=> 6, 'shipment_status_id'=> NULL,'name' => 'Unresponsive', 'shipment_status_name' => 'Unresponsive','call_finding_id'=> 1, 'created_at' => $timestamp, 'updated_at'=>$timestamp, 'is_visible'=> 1 ),
            array('id'=> 7, 'shipment_status_id' => 65, 'name' => 'Shipper Advised Requested', 'shipment_status_name' => 'Shipper Advised Requested', 'call_finding_id' => NULL, 'created_at' => $timestamp, 'updated_at'=>$timestamp, 'is_visible'=> 0 ),
        ));
    }
}
