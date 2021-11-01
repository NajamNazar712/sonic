<?php

use Illuminate\Database\Seeder;

class UpdateAppNotificationForEmployeeLeavesNotification extends Seeder
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
            array('id' => 11, 'name' => 'Leave Request Update - Employee', 'title' => 'Leave Request Update', 'body' => 'Your Leave Request [from] to [to] has been [status].', 'app_id' => 1,'updated_by' => 664,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 12, 'name' => 'Leave Request Update - HOD', 'title' => 'Leave Request Update', 'body' => '[employee_name] ([trax_id]) has submit leave request [from] to [to] is awaiting for your approval.', 'app_id' => 1,'updated_by' => 664,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 13, 'name' => 'Leave Request Update - HR', 'title' => 'Leave Request Update', 'body' => 'Leave request of [employee_name] ([trax_id]) [from] to [to] has Approve by HOD, awaiting for your approval.', 'app_id' => 1,'updated_by' => 664,'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));
    }
}
