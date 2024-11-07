<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForAppNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 601, 'name' => 'App Notifications - View', 'module_id' => 13),
            array('id' => 602, 'name' => 'App Notifications - Update', 'module_id' => 13),
            array('id' => 603, 'name' => 'App Notifications - Enable/Disable', 'module_id' => 13)
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 450, 'screen_name' => 'App-Notification', 'action'=> 'View')
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Support > App-Notification', 'url'=>'admin.app_notifications.index', 'permission_id' => 601)
        );
    }
}
