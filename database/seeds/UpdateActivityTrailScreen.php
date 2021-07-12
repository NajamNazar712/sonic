<?php

use Illuminate\Database\Seeder;

class UpdateActivityTrailScreen extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 273, 'screen_name' => 'Receiving Sheet History', 'action'=> 'View'),
            array('id' => 274, 'screen_name' => 'Receiving Sheet History', 'action'=> 'Excel Download'),
            array('id' => 275, 'screen_name' => 'Blocked Accounts List', 'action'=> 'View'),
            array('id' => 276, 'screen_name' => 'Blocked Accounts List', 'action'=> 'Excel Download'),
            array('id' => 277, 'screen_name' => 'Merged Accounts List', 'action'=> 'View'),
            array('id' => 278, 'screen_name' => 'Merged Accounts List', 'action'=> 'Excel Download'),
            array('id' => 279, 'screen_name' => 'Today Active Accounts List', 'action'=> 'View'),
            array('id' => 280, 'screen_name' => 'Today Active Accounts List', 'action'=> 'Excel Download'),
            array('id' => 281, 'screen_name' => 'Packaging Material Requests', 'action'=> 'View'),
            array('id' => 282, 'screen_name' => 'Today Active Accounts List', 'action'=> 'Excel Download'),
            
        ));
    }
}
