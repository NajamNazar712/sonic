<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateNotificationForOutstandingShipmentsReportTableSeeder extends Seeder
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
            array('id' => 76, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Outstanding Shipments Report', 'type_id' => 1, 'subject' => 'Outstanding Shipments Report [hub] [date]', 'body' => 'Please download the report from the following link: [link].', 'updated_by' => 7, 'status' => 0)
        ));
    }
}
