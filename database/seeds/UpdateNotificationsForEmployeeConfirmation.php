<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UpdateNotificationsForEmployeeConfirmation extends Seeder
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
            array('id' => 182, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Employee Confirmation', 'type_id' => 1, 'subject' => 'Employee Confirmation Request [emp_id] [name]', 'body' => 'Dear Concern,'. PHP_EOL .  PHP_EOL .'The Employee Confirmation Request of below staff awaits your approval.'. PHP_EOL .  PHP_EOL .'[link]'. PHP_EOL .  PHP_EOL .'Trax Id : [emp_id]'. PHP_EOL .'Name : [name]'. PHP_EOL .'Designation : [designation]'. PHP_EOL .'Joining Date: [joining_date]', 'updated_by' => 7, 'status' => 1)
        ));
    }
}
