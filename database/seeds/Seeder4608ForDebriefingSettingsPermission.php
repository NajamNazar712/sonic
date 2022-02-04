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
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 501, 'screen_name' => 'Debriefing Break Time Settings', 'action'=> 'View'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Last Mile > Debriefing Break Time Settings', 'url'=>'admin.settings.debriefing_break_time.index', 'permission_id' => 674));

        DB::table('global_settings')->insert(array(
            array('type' => 'debriefing_break_time_setting','text' => 1, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
