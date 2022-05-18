<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForUndeliveredSMSHubWise extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 714, 'name' => 'Undelivered SMS Hub Wise', 'module_id' => 14)
        ));


        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Last Mile > Undelivered SMS Hub Wise', 'url'=>'admin.settings.undelivered_sms_hub_wise.index', 'permission_id' => 714),
        ));
        
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 529, 'screen_name' => 'Undelivered SMS Hub Wise', 'action'=> 'View'),
        ));
    }
}
