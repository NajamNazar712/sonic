<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForAttendanceAdjustment extends Seeder
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
                array('id' => 212, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Attendance Adjustment', 'type_id' => 1, 'subject' => 'Attendance Adjustment for [employee_name].', 'body' => 'Dear Concerned, [employee_name] has added adjustment of attendance please proceed at you earliest.', 'updated_by' => 3, 'status' => 1),
            ));
    }
}
