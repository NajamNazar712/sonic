<?php

use Illuminate\Database\Seeder;

class CreateActivityTrailAndPermissionForV3PickupRequest extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 920, 'name' => 'Pickup Request  - View', 'module_id' => 3),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 722, 'screen_name' => 'Pickup Request', 'action'=> 'View'),
            array('id' => 723, 'screen_name' => 'Pickup Request', 'action'=> 'Excel'),

        ));

        // $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        // DB::table('admins_screen_list')->insert(array(
        //     array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'First Mile > Pickup > V3 Pickup Request', 'url'=>'admin.v3_pickups.pending.index', 'permission_id' => 920),           
        // ));


        
    }
}
