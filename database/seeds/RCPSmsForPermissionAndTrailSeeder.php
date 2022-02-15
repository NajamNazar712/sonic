<?php

use Illuminate\Database\Seeder;

class RCPSmsForPermissionAndTrailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 680, 'name' => 'RCP SMS Settings - View', 'module_id' => 14),
        ));


        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile > Return > Return Confirmation Pending SMS', 'url'=>'admin.return.confirmation_pending_sms', 'permission_id' => 675),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Last Mile > Return Confirmation Pending SMS', 'url'=>'admin.settings.rcp_sms.index', 'permission_id' => 680),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 508, 'screen_name' => 'RCP SMS Settings ', 'action'=> 'View'),
        ));
    }
}
