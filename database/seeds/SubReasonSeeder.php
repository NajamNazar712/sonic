<?php

use Illuminate\Database\Seeder;

class SubReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('sub_reasons')->insert(array(
            array('id' => 1, 'name' => 'Consignee Wants To Open The Shipment', 'reason_id' => '8', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 2, 'name' => 'Issue In The COD Amount/Product', 'reason_id' => '8', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 3, 'name' => 'No Such Order From Consignee', 'reason_id' => '8', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 4, 'name' => 'Refused After Opening The Shipment', 'reason_id' => '8', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 5, 'name' => 'Delay in Dispatched from Shipper', 'reason_id' => '8', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 6, 'name' => 'Delay in Delivery From TRAX', 'reason_id' => '8', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 7, 'name' => 'Duplicate order', 'reason_id' => '8', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 8, 'name' => 'Purchased From Outlet', 'reason_id' => '8', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 9, 'name' => 'Quality Issue', 'reason_id' => '8', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 10, 'name' => 'Change of Mind', 'reason_id' => '8', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 11, 'name' => 'Place another order', 'reason_id' => '8', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 12, 'name' => 'Other', 'reason_id' => '8', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 13, 'name' => 'House No', 'reason_id' => '3', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 14, 'name' => 'Plot No', 'reason_id' => '3', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 15, 'name' => 'Area Name', 'reason_id' => '3', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 16, 'name' => 'Street No', 'reason_id' => '3', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 17, 'name' => 'Street Name', 'reason_id' => '3', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 18, 'name' => 'Sector No', 'reason_id' => '3', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 19, 'name' => 'Floor No', 'reason_id' => '3', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 20, 'name' => 'Office No', 'reason_id' => '3', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 21, 'name' => 'Building No', 'reason_id' => '3', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 22, 'name' => 'Other', 'reason_id' => '3', 'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));
    }
}
