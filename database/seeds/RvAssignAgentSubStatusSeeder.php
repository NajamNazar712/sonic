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

        DB::table('rv_assign_agent_sub_statuses')->truncate();
        
        DB::table('rv_assign_agent_sub_statuses')->insert(array(


            //Return Confirm Reasons in Shipment Status Reason Table
            // array('name' => 'Issue in the Product','shipment_status_reason_id'=> 47,'rv_assign_agent_status_id'=> 1,'is_active'=>1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            // array('name' => 'Issue in the COD Amount','shipment_status_reason_id'=> 9,'rv_assign_agent_status_id'=> 1,'is_active'=>1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            // array('name' => 'Consignee Unavailable','shipment_status_reason_id'=> 52,'rv_assign_agent_status_id'=> 1,'is_active'=>1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            // array('name' => 'No such order/consignee','shipment_status_reason_id'=> 49'rv_assign_agent_status_id'=> 1,'is_active'=>1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            // array('name' => 'Delay in Delivery','shipment_status_reason_id'=> 39,'rv_assign_agent_status_id'=> 1,'is_active'=>1,'created_at' => $timestamp, 'updated_at' => $timestamp),


            //Return Confirm Reasons Not Found in Shipment Status Reason Table
            // array('name' => 'Consignee is not interested','shipment_status_reason_id'=> Null,'rv_assign_agent_status_id'=> 1,'is_active'=>1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            // array('name' => 'Consignee wants to open the shipment','shipment_status_reason_id'=> Null,'rv_assign_agent_status_id'=> 1,'is_active'=>1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            // array('name' => 'Duplicate Order','shipment_status_reason_id'=> Null,'rv_assign_agent_status_id'=> 1,'is_active'=>1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            // array('name' => 'Number Not Pertain to Consignee','shipment_status_reason_id'=> Null,'rv_assign_agent_status_id'=> 1,'is_active'=>1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            // array('name' => 'Refused after opening the shipment','shipment_status_reason_id'=> Null,'rv_assign_agent_status_id'=> 1,'is_active'=>1,'created_at' => $timestamp, 'updated_at' => $timestamp),


            //Return Confirm Reasons in Shipment Status Reason Table
            array('name' => 'Consignee Unresponsive on Phone call','shipment_status_reason_id'=> 2,'rv_assign_agent_status_id'=> 0,'is_active'=> 0,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Consignee Out Of City','shipment_status_reason_id'=> 5,'rv_assign_agent_status_id'=> 1,'is_active'=> 0,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Issue in the COD Amount','shipment_status_reason_id'=> 9,'rv_assign_agent_status_id'=> 1,'is_active'=> 1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Consignee is not Responding','shipment_status_reason_id'=> 13,'rv_assign_agent_status_id'=> 1,'is_active'=> 0,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Consignee Refused','shipment_status_reason_id'=> 38,'rv_assign_agent_status_id'=> 1,'is_active'=> 0,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Delay in Delivery','shipment_status_reason_id'=> 39,'rv_assign_agent_status_id'=> 1,'is_active'=> 1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Address Issue','shipment_status_reason_id'=> 46,'rv_assign_agent_status_id'=> 1,'is_active'=> 0,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Issue in the Product','shipment_status_reason_id'=> 47,'rv_assign_agent_status_id'=> 1,'is_active'=> 1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Shipment Damage','shipment_status_reason_id'=> 48,'rv_assign_agent_status_id'=> 1,'is_active'=> 0,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'No such order/consignee','shipment_status_reason_id'=> 49,'rv_assign_agent_status_id'=> 1,'is_active'=> 1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'NSA / OSA Parcel','shipment_status_reason_id'=> 50,'rv_assign_agent_status_id'=> 1,'is_active'=> 0,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Consignee Unavailable','shipment_status_reason_id'=> 52,'rv_assign_agent_status_id'=> 1,'is_active'=> 1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'As per Shipper Request','shipment_status_reason_id'=> 53,'rv_assign_agent_status_id'=> 1,'is_active'=> 0,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Wants to Open2','shipment_status_reason_id'=> 54,'rv_assign_agent_status_id'=> 1,'is_active'=> 0,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Consignee Unresponsive','shipment_status_reason_id'=> 55,'rv_assign_agent_status_id'=> 1,'is_active'=> 0,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Delay in Dispatch from Shipper','shipment_status_reason_id'=> 59,'rv_assign_agent_status_id'=> 1,'is_active'=> 0,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'A OPEN','shipment_status_reason_id'=> 62,'rv_assign_agent_status_id'=> 1,'is_active'=> 0,'created_at' => $timestamp, 'updated_at' => $timestamp),

            //Return Confirm Reasons Not Found in Shipment Status Reason Table
            array('name' => 'Consignee is not interested','shipment_status_reason_id'=> Null,'rv_assign_agent_status_id'=> 1,'is_active'=> 1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Consignee wants to open the shipment','shipment_status_reason_id'=> Null,'rv_assign_agent_status_id'=> 1,'is_active'=> 1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Duplicate Order','shipment_status_reason_id'=> Null,'rv_assign_agent_status_id'=> 1,'is_active'=> 1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Number Not Pertain to Consignee','shipment_status_reason_id'=> Null,'rv_assign_agent_status_id'=> 1,'is_active'=> 1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Refused after opening the shipment','shipment_status_reason_id'=> Null,'rv_assign_agent_status_id'=> 1,'is_active'=> 1,'created_at' => $timestamp, 'updated_at' => $timestamp),


            array('name' => 'Mark for self collection','shipment_status_reason_id'=> Null,'rv_assign_agent_status_id'=> 5,'is_active'=> 1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'No One Came For Self-Collection','shipment_status_reason_id'=> 45,'rv_assign_agent_status_id'=> 5,'is_active'=> 0,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'NSA / OSA Hold','shipment_status_reason_id'=> Null,'rv_assign_agent_status_id'=> 5, 'is_active'=> 0, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Hold','shipment_status_reason_id'=> Null,'rv_assign_agent_status_id'=> 5, 'is_active'=> 0, 'created_at' => $timestamp, 'updated_at' => $timestamp),

            //Unresponsive Reasons
            array('name' => 'Invalid','shipment_status_reason_id'=> Null,'rv_assign_agent_status_id'=> 6,'is_active'=> 1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Powered Off','shipment_status_reason_id'=> Null,'rv_assign_agent_status_id'=> 6,'is_active'=> 1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Not Answered','shipment_status_reason_id'=> Null,'rv_assign_agent_status_id'=> 6,'is_active'=> 1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Hang up by Customer','shipment_status_reason_id'=> Null,'rv_assign_agent_status_id'=> 6,'is_active'=> 1,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'Number Busy','shipment_status_reason_id'=> Null,'rv_assign_agent_status_id'=> 6,'is_active'=> 1,'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));
    }
}
