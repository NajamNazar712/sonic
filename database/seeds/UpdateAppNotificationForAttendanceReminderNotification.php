<?php

use Illuminate\Database\Seeder;

class UpdateAppNotificationForAttendanceReminderNotification extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('app_notifications')->insert(array(
            array('id' => 10, 'name' => 'Attendance Reminder', 'title' => 'Attendance Reminder', 'body' => 'Dear [name],' . PHP_EOL. 'Please Mark Your Attendance Before [time].', 'app_id' => 1,'updated_by' => 664,'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
