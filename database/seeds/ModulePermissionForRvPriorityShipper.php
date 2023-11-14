<?php

use Illuminate\Database\Seeder;

class ModulePermissionForRvPriorityShipper extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 904, 'name' => 'Setting - RV Priority Shipper', 'module_id' => 14),
        ));


        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Shippers > RV Priority Shipper', 'url'=>'admin.settings.rv_shipper_priority.index', 'permission_id' => 904),           
        ));

    }
}
