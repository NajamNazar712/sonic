<?php

use Illuminate\Database\Seeder;

class UpdateActivityTrailSeederForReversePickupReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 470, 'screen_name' => 'Reverse Pickup', 'action'=> 'View'),
            array('id' => 471, 'screen_name' => 'Reverse Pickup', 'action'=> 'Excel Download'),
        ));
    }
}
