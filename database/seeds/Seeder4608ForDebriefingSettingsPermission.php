<?php

use Illuminate\Database\Seeder;

class Seeder4608ForDebriefingSettingsPermission extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 674, 'name' => 'Debriefing Break Time Settings', 'module_id' => 14),
            array('id' => 676, 'name' => 'Debriefing Agent Report', 'module_id' => 9),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 501, 'screen_name' => 'Debriefing Break Time Settings', 'action'=> 'View'),
            array('id' => 504, 'screen_name' => 'Debriefing Agent Report', 'action'=> 'View'),
            array('id' => 505, 'screen_name' => 'Debriefing Agent Report', 'action'=> 'Excel Download'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Last Mile > Debriefing Break Time Settings', 'url'=>'admin.settings.debriefing_break_time.index', 'permission_id' => 674),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Debriefing Agent Report', 'url'=>'admin.reports.debriefing.agent_index', 'permission_id' => 676),
        ));
        
        DB::table('global_settings')->insert(array(
            array('type' => 'debriefing_break_time_setting','text' => 1, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
