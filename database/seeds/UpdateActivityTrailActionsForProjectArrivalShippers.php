<?php

use Illuminate\Database\Seeder;

class UpdateActivityTrailActionsForProjectArrivalShippers extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 629, 'screen_name' => 'Project Shipper Arrival of Shipments', 'action'=> 'View'),
            array('id' => 630, 'screen_name' => 'Project Arrival Shippers Setting', 'action'=> 'View'),
            array('id' => 631, 'screen_name' => 'Project Arrival Shippers Setting', 'action'=> 'Update'),
        ));
    }
}
