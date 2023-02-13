<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForAttendanceAdjustmentShiftWise extends Seeder
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
                array('id' => 211, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Attendance Adjustment Shift Wise', 'type_id' => 1, 'subject' => 'Attendance Adjustment', 'body' => 'Your attendance for [Date&Day] is not marked in system. Please add the adjustment on Bolt at your earliest.', 'updated_by' => 3, 'status' => 1),
            ));
    }
}
