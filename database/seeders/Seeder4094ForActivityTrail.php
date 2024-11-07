<?php

use Illuminate\Database\Seeder;

class Seeder4094ForActivityTrail extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 453, 'screen_name' => 'Employee Attendance (Horizontal)', 'action'=> 'View'),
            array('id' => 454, 'screen_name' => 'Employee Attendance (Horizontal)', 'action'=> 'Excel Download')
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Human Resource > Emploee Attendance (Horizontal)', 'url'=>'admin.attendance.horizontal.index', 'permission_id' => 465)
        );
    }
}
