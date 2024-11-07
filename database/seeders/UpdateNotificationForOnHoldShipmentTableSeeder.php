<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForOnHoldShipmentTableSeeder extends Seeder
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
            array('id' => 108, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Dispatch On-Hold Shipments', 'type_id' => 1, 'subject' => 'Today is dispatch date [date]', 'body' => 'Dear Concern,' . PHP_EOL .'Today is dispatch date for below listed shipments.'. PHP_EOL . '[preview]', 'updated_by' => 7, 'status' => 0),
            array('id' => 109, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Delivery On-Hold Shipments', 'type_id' => 1, 'subject' => 'Today is delivery date [date]', 'body' => 'Dear Concern,' . PHP_EOL .'Today is delivery date for below listed shipments.'. PHP_EOL . '[preview]', 'updated_by' => 7, 'status' => 0)
        ));
    }
}
