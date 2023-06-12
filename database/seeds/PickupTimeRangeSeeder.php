<?php

use Illuminate\Database\Seeder;

class PickupTimeRangeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pickup_time_ranges')->insert(array(
        array('id' => 1, 'start_time' => '09:00:00', 'end_time' => '10:00:00' ,'status'=> 1),
        array('id' => 2, 'start_time' => '10:00:00', 'end_time' => '11:00:00' ,'status'=> 1),
        array('id' => 3, 'start_time' => '11:00:00', 'end_time' => '12:00:00' ,'status'=> 1),
        array('id' => 4, 'start_time' => '12:00:00', 'end_time' => '13:00:00' ,'status'=> 1),
        array('id' => 5, 'start_time' => '13:00:00', 'end_time' => '14:00:00' ,'status'=> 1),
        array('id' => 6, 'start_time' => '14:00:00', 'end_time' => '15:00:00' ,'status'=> 1),
        array('id' => 7, 'start_time' => '15:00:00', 'end_time' => '16:00:00' ,'status'=> 1),
        array('id' => 8, 'start_time' => '16:00:00', 'end_time' => '17:00:00' ,'status'=> 1),
        array('id' => 9, 'start_time' => '17:00:00', 'end_time' => '18:00:00' ,'status'=> 1),
    ));
    }
}
