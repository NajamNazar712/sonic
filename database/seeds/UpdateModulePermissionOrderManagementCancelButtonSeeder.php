<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionOrderManagementCancelButtonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 888, 'name' => 'Order Management (Cancel) - Action', 'module_id' => 33),
        ));
       
        DB::table('admin_role_module_permissions')->insert(array(
            array('role_id' => 91, 'permission_id' => 888),
            array('role_id' => 19, 'permission_id' => 888),
            array('role_id' => 106, 'permission_id' => 888),
        ));

    }
}
