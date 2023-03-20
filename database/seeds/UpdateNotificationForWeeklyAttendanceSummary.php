<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateNotificationForWeeklyAttendanceSummary extends Seeder
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
            array('id' => 209, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Weekly Attendance Summary Notification', 'type_id' => 1, 'subject' => 'Attendance Log', 'body' => 'Dear [line_manager],' . PHP_EOL . ' Please find attached document for updating attendance.' . PHP_EOL . '[preview]'. PHP_EOL . '[link]', 'updated_by' => 3, 'status' => 1),
        ));
    }
}
