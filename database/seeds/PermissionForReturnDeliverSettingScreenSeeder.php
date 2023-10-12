<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PermissionForReturnDeliverSettingScreenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('module_permissions')->insert(array(
            array('id' => 861, 'name' => 'Return Deliver Setting - View', 'module_id' => 14),
        ));

// if new screen or excel
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 655, 'screen_name' => 'Return Deliver Setting', 'action'=> 'View'),
        ));

// if new screen
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Setting > Shipper > SMS Notifications Limit', 'url'=>'admin.settings.sms_notifications_limit.index', 'permission_id' => 861),
        ));
    }
}
