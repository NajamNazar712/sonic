<?php

use Illuminate\Database\Seeder;

class RiderAssignedHubForDeliveryNotePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('module_permissions')->insert(array(
            array('id' => 893, 'name' => 'Rider Assigned Hub - View', 'module_id' => 14),
            array('id' => 898, 'name' => 'Rider Assigned Hub (Edit)- button', 'module_id' => 14),
        ));
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 686, 'screen_name' => 'Rider Assigned Hub', 'action'=> 'View'),
        ));
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp,
                'name' => 'Setting > Last Mile > Rider Assigned Hub',
                'url'=>'admin.settings.rider_assigned_hub.index', 'permission_id' => 893),
        ));
    }
}
