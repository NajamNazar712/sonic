<?php

use Illuminate\Database\Seeder;

class UpdateModuleForReturnedDeliveredToShipper extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 861, 'name' => 'Return Shipments', 'module_id' => 14),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 655, 'screen_name' => 'Return Shipments', 'action'=> 'View'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > SMS Notifications Limit', 'url'=>'admin.settings.sms_notifications_limit.index', 'permission_id' => 861),
        ));
    }
}
