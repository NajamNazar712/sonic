<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForTeamLeadManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 903, 'name' => 'Team Lead', 'module_id' => 25),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 703, 'screen_name' => 'Team Lead', 'action'=> 'View'),
          
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Team Lead', 'url'=>'admin.team_lead.index', 'permission_id' => 903),           
        ));

    }
}
