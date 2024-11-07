<?php

use Illuminate\Database\Seeder;

class UpdateAdminScreenNameForEmployeeAttendance extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins_screen_list')->where('id', 359)->update(['name' => 'Human Resource > Employee Attendance (Horizontal)']);
    }
}
