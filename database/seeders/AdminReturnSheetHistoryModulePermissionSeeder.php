<?php

use Illuminate\Database\Seeder;

class AdminReturnSheetHistoryModulePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 885, 'name' => 'Shipper Return Receiving History - View', 'module_id' => 7),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 677, 'screen_name' => 'Shipper Return Receiving History', 'action'=> 'View'),
            array('id' => 678, 'screen_name' => 'Shipper Return Receiving History', 'action'=> 'Excel Download'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mule > Return > Shipper Return Receiving History', 'url'=>'admin.return.shipper_return_receiving.history.index', 'permission_id' => 885),
        ));
    }
}
