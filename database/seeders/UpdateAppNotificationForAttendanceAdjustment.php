<?php

use Illuminate\Database\Seeder;

class UpdateAppNotificationForAttendanceAdjustment extends Seeder
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
            array('id' => 17, 'name' => 'Adjustment Request Update - Employee', 'title' => 'Adjustment Request Update', 'body' => 'Your Adjustment Request of date [date] has been [status].', 'app_id' => 1,'updated_by' => 664,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 18, 'name' => 'Adjustment Request Update - HOD', 'title' => 'Adjustment Request Update', 'body' => '[employee_name] ([trax_id]) has submit adjustment request of date [date] is awaiting for your approval.', 'app_id' => 1,'updated_by' => 664,'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));
    }
}
