<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateSettingsLeadProgressSettingPermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    { 
        DB::table('module_permissions')->insert(array(
            array('id' => 989, 'name' => 'Lead Progress Setting - View', 'module_id' => 14),
        ));

        
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 795, 'screen_name' => 'Lead Progress Setting ', 'action'=> 'View'),

        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Setting > Shippers > Lead Progress Setting', 'url'=>'admin.settings.shippers.lead_progress.index', 'permission_id' => 989),           
        ));
    }
}