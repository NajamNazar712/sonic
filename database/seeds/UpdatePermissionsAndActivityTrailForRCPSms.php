<?php

use Illuminate\Database\Seeder;

class UpdatePermissionsAndActivityTrailForRCPSms extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 675, 'name' => 'RCP - SMS Screen', 'module_id' => 7),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 502, 'screen_name' => 'RCP SMS', 'action'=> 'View'),
            array('id' => 503, 'screen_name' => 'RCP SMS', 'action'=> 'Excel Download'),
        ));
    }
}
