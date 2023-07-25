<?php

use Illuminate\Database\Seeder;

class RvAssignAgentSubStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('rv_assign_agent_sub_statuses')->insert(array(
            array('name' => 'Issue in the Product','rv_assign_agent_status_id'=> 1,'is_active'=>1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Issue in the COD Amount','rv_assign_agent_status_id'=> 1,'is_active'=>1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Consignee is not interested','rv_assign_agent_status_id'=> 1,'is_active'=>1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Consignee Unavailable','rv_assign_agent_status_id'=> 1,'is_active'=>1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Consignee wants to open the shipment','rv_assign_agent_status_id'=> 1,'is_active'=>1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Duplicate Order','rv_assign_agent_status_id'=> 1,'is_active'=>1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'No such order from consignee','rv_assign_agent_status_id'=> 1,'is_active'=>1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Number Not Pertain to Consignee','rv_assign_agent_status_id'=> 1,'is_active'=>1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Refused after opening the shipment','rv_assign_agent_status_id'=> 1,'is_active'=>1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Delay in Delivery','rv_assign_agent_status_id'=> 1,'is_active'=>1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Mark for self collection','rv_assign_agent_status_id'=> 5,'is_active'=>1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'NSA / OSA Hold','rv_assign_agent_status_id'=> 5, 'is_active'=> 0, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Hold','rv_assign_agent_status_id'=> 5, 'is_active'=> 0, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Invalid','rv_assign_agent_status_id'=> 6,'is_active'=>1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Powered Off','rv_assign_agent_status_id'=> 6,'is_active'=>1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Not Answered','rv_assign_agent_status_id'=> 6,'is_active'=>1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Hang up by Customer','rv_assign_agent_status_id'=> 6,'is_active'=>1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Number Busy','rv_assign_agent_status_id'=> 6,'is_active'=>1,'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));
    }
}
