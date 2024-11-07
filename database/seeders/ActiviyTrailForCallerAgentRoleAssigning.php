<?php

use Illuminate\Database\Seeder;

class ActiviyTrailForCallerAgentRoleAssigning extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 654, 'screen_name' => 'Caller Agent Role Assigning', 'action'=> 'View'),
          
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Last Mile > Caller Agent Role Assigning', 'url'=>'admin.settings.debriefing_time_setting.index', 'permission_id' => 858),           
        ));
    }
}
