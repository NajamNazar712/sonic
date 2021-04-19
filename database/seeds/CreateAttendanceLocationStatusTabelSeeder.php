<?php

use Illuminate\Database\Seeder;

class CreateAttendanceLocationStatusTabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('attendance_location_statuses')->insert(array(
            array('id'=>1, 'name'=>'OFF-Site'),
            array('id'=>2, 'name'=>'ON-Site')
        ));
    }
}
