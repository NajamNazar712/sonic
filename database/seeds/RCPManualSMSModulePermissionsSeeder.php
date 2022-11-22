<?php

use Illuminate\Database\Seeder;

class RCPManualSMSModulePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 781, 'name' => 'RCP Manual SMS Log - View', 'module_id' => 7),
            array('id' => 782, 'name' => 'RCP Manual SMS Log Excel - View', 'module_id' => 7),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 570, 'screen_name' => 'RCP Manual SMS Log', 'action'=> 'View'),
            array('id' => 571, 'screen_name' => 'RCP Manual SMS Log Excel', 'action'=> 'Download'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile > Return > RCP Manual SMS Log', 'url'=>'admin.return.confirmation_pending_manual_sms', 'permission_id' => 781),
        ));

        // add permission according to instruction of ticket TO-5413
        DB::table('admin_role_module_permissions')->insert(array(
            array('role_id' => 49, 'permission_id' => 781),
            array('role_id' => 49, 'permission_id' => 782),

            array('role_id' => 21, 'permission_id' => 781),
            array('role_id' => 21, 'permission_id' => 782),

            array('role_id' => 26, 'permission_id' => 781),
            array('role_id' => 26, 'permission_id' => 782),

            array('role_id' => 32, 'permission_id' => 781),
            array('role_id' => 32, 'permission_id' => 782),

            array('role_id' => 37, 'permission_id' => 781),
            array('role_id' => 37, 'permission_id' => 782),

            array('role_id' => 6, 'permission_id' => 781),
            array('role_id' => 6, 'permission_id' => 782),

            array('role_id' => 83, 'permission_id' => 781),
            array('role_id' => 83, 'permission_id' => 782),
        ));


    }
}
