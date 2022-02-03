<?php

use Illuminate\Database\Seeder;

class UpdateActivityTrailForLeadManagement extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 493, 'screen_name' => 'Leads Auto Tagging', 'action'=> 'View'),
            array('id' => 494, 'screen_name' => 'Leads Auto Tagging', 'action'=> 'Excel Download'),
            array('id' => 495, 'screen_name' => 'Leads Zone Tagging', 'action'=> 'View'),
            array('id' => 496, 'screen_name' => 'Leads Zone Tagging', 'action'=> 'Excel Download'),
            array('id' => 497, 'screen_name' => 'Leads Notification', 'action'=> 'View'),
            array('id' => 498, 'screen_name' => 'Leads Notification', 'action'=> 'Excel Download'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( 
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Leads Management > Lead Auto Tagging', 'url'=>'admin.settings.lead_tagging.index', 'permission_id' => 661),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Leads Management > Lead Zone Tagging', 'url'=>'admin.settings.lead_zones.index', 'permission_id' => 664),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Leads Management > Leads Notifications', 'url'=>'admin.settings.lead_notification.index', 'permission_id' => 671),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'First Mile > v2 Pickups > Rider Receiving DWS', 'url'=>'v2_pickups.rider_receiving.dws.index', 'permission_id' => 670)
        );
    }
}
