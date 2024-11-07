<?php

use Illuminate\Database\Seeder;

class UpdateAppNotificationForMarkAttendance extends Seeder
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
            array('id' => 20, 'name' => 'Attendance', 'title' => 'Attendance', 'body' => 'Please Mark Your Attendance', 'app_id' => 1,'updated_by' => 664,'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
