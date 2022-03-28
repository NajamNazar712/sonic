<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForLeadEditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 696, 'name' => 'Lead Edit - Action', 'module_id' => 25),
        ));

        DB::table('admin_role_module_permissions')->insert(array(
           array('role_id' => 4, 'permission_id' => 696),
           array('role_id' => 43, 'permission_id' => 696),
        ));
    }
}
