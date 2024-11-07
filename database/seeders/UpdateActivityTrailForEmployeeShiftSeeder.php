<?php

use Illuminate\Database\Seeder;

class UpdateActivityTrailForEmployeeShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 433, 'screen_name' => 'Employee Shifts', 'action'=> 'View'),
            array('id' => 434, 'screen_name' => 'Employee Shifts', 'action'=> 'Excel Download'),
        ));
    }
}
