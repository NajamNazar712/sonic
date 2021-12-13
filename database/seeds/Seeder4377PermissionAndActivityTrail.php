<?php

use Illuminate\Database\Seeder;

class Seeder4377PermissionAndActivityTrail extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 478, 'screen_name' => 'Shippers Status Webhook Subscription', 'action'=> 'View'),
            array('id' => 479, 'screen_name' => 'Shippers Status Webhook Subscription', 'action'=> 'Excel Download'),
            array('id' => 480, 'screen_name' => 'Shippers Status Webhook Subscription', 'action'=> 'Status Update'),
        ));
        DB::table('module_permissions')->insert(array(
            array('id' => 646, 'name' => 'Shippers Status Webhook Subscription', 'module_id' => 14),
        ));
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Shippers > Shippers Status Webhook Subscription', 'url'=>'admin.settings.shippers.status_webhook.index', 'permission_id' => 646));

    }
}
