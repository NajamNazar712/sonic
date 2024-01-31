<?php

use Illuminate\Database\Seeder;

class CreateActivityTrailAndPermissionForSchedulePickups extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 919, 'name' => 'Schedule Pickups - View', 'module_id' => 3),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 720, 'screen_name' => 'Schedule Pickups', 'action'=> 'View'),
            array('id' => 721, 'screen_name' => 'Schedule Pickups', 'action'=> 'Excel'),

        ));

        // $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        // DB::table('admins_screen_list')->insert(array(
        //     array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'First Mile > Pickup > V3 Schedule Pickups', 'url'=>'admin.v3_pickups.pending.schedule.index', 'permission_id' => 919),           
        // ));
    }
}
