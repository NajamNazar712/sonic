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
    }
}
