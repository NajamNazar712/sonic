<?php

use Illuminate\Database\Seeder;

class CreateActivityTrailAndPermissionForV3PickupRequestHistory extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 921, 'name' => 'Pickup Request History - View', 'module_id' => 3),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 724, 'screen_name' => 'Pickup Request History', 'action'=> 'View'),
            array('id' => 725, 'screen_name' => 'Pickup Request History', 'action'=> 'Excel'),

        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'First Mile > Pickup > V3 Pickup Request History', 'url'=>'admin.v3_pickups.history.index', 'permission_id' => 921),           
        ));
    }
}
