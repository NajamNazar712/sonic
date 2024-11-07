<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForPickupHistoryCnWiseScreen extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 679, 'name' => 'Pickup History CN Wise - View', 'module_id' => 9),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 506, 'screen_name' => 'Pickup History CN Wise', 'action'=> 'View'),
            array('id' => 507, 'screen_name' => 'Pickup History CN Wise', 'action'=> 'Excel Download'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Pickup History (CN Wise)', 'url'=>'admin.reports.pickup_history_cn_wise.index', 'permission_id' => 679));
    }
}
