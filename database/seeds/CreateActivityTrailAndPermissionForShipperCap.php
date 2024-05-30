<?php

use Illuminate\Database\Seeder;

class CreateActivityTrailAndPermissionForShipperCap extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('module_permissions')->insert(array(
            array('id' => 942, 'name' => 'Shipper Cap Setting - View', 'module_id' => 14),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' =>  752, 'screen_name' => 'Shipper Cap Setting', 'action'=> 'View'),
            // array('id' => 753, 'screen_name' => 'Shipper Cap Setting', 'action'=> 'Excel'),

        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Shippers > Shipper Cap', 'url'=>'admin.settings.shipper_cap.index', 'permission_id' => 942),           
        ));
    }
}
