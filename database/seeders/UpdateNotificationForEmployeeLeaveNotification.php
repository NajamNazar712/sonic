<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateNotificationForEmployeeLeaveNotification extends Seeder
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
            array('id' => 208, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Employee Leave Notification', 'type_id' => 1, 'subject' => 'Leave Application [employee_name]', 'body' => '[employee_name] having employee ID [emp_id] has applied for leaves. Please check and proceed.', 'updated_by' => 3, 'status' => 1),
        ));
    }
}
