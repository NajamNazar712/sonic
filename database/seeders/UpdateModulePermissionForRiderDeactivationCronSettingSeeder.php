<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForRiderDeactivationCronSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 707, 'name' => 'Rider Deactivation Cron Setting', 'module_id' => 14),
        ));

        DB::table('admin_role_module_permissions')->insert(array(
            array('permission_id' => 707, 'role_id' => 70),
            array('permission_id' => 707, 'role_id' => 69),
            array('permission_id' => 707, 'role_id' => 63),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 525, 'screen_name' => 'Rider Deactivation Cron Settings', 'action'=> 'View'),
            array('id' => 526, 'screen_name' => 'Rider Deactivation Cron Settings', 'action'=> 'Update'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Last Mile > Network Management > Riders > Rider Deactivation Cron', 'url'=>'admin.settings.rider_deactivation_cron.index', 'permission_id' => 707),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Last Mile > Network Management > Riders > Rider Shipments Attempt', 'url'=>'admin.settings.rider_shipment_attempt.index', 'permission_id' => 562)
        ));

    }
}
