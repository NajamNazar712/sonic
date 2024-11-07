<?php

use Illuminate\Database\Seeder;

class AddAttendanceActionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('attendance_actions')->insert(array(
            array('id' => 1, 'name' => 'Clock-IN'),
            array('id' => 2, 'name' => 'Clock-Out'),
        ));
    }
}
