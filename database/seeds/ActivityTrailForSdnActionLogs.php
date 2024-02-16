<?php

use Illuminate\Database\Seeder;

class ActivityTrailForSdnActionLogs extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 751, 'screen_name' => 'SDN acions logs', 'action'=> 'View'),
        ));
        DB::table('module_permissions')->insert(array(
            array('id' => 941, 'name' => 'SDN acions logs - View', 'module_id' => 6)

        ));
    }
}
