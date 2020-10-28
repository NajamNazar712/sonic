<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForOverallPickupVendorWiseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        DB::table('notifications')->insert(array(
            array('id' => 101, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Vendor Pickup Hub Wise', 'type_id' => 1, 'subject' => 'Vendor Pickup [hub] [date]', 'body' => '[preview]', 'updated_by' => 7, 'status' => 0),
            array('id' => 102, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Vendor Pickup Zone Wise', 'type_id' => 1, 'subject' => 'Vendor Pickup [zone] [date]', 'body' => '[preview]', 'updated_by' => 7, 'status' => 0),
            array('id' => 103, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Vendor Pickup Overall', 'type_id' => 1, 'subject' => 'Vendor Pickup Overall [date]', 'body' => '[preview]', 'updated_by' => 7, 'status' => 0),
        ));
    }
}
