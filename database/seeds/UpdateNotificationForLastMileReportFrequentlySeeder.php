<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForLastMileReportFrequentlySeeder extends Seeder
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
            array('id' => 147, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile Status Report Hourly', 'type_id' => 1, 'subject' => 'Last Mile Status Report Hourly [time]', 'body' => '[preview]', 'updated_by' => 6, 'status' => 0)
        ));
    }
}
