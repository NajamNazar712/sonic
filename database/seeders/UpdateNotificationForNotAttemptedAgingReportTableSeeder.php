<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateNotificationForNotAttemptedAgingReportTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('notifications')->insert(array(
            array('id' => 68, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Not Attempted Report Hub Wise', 'type_id' => 1, 'subject' => 'Not Attempted Report [hub] [date]', 'body' => '[preview]', 'updated_by' => 7, 'status' => 0),
            array('id' => 69, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Not Attempted Report Zone Wise', 'type_id' => 1, 'subject' => 'Not Attempted Report [zone] [date]', 'body' => '[preview]', 'updated_by' => 7, 'status' => 0),
            array('id' => 70, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Not Attempted Report Overall', 'type_id' => 1, 'subject' => 'Not Attempted Report Overall [date]', 'body' => '[preview]', 'updated_by' => 7, 'status' => 0),
        ));
    }
}
