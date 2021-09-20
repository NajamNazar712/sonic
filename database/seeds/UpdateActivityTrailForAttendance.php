<?php

use Illuminate\Database\Seeder;

class UpdateActivityTrailForAttendance extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 432, 'screen_name' => 'Attendance', 'action'=> 'View'),
        ));
    }
}
