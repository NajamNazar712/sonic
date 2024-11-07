<?php

use Illuminate\Database\Seeder;

class RolePermissionModulePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('module_permissions')->insert(array(
            array('id' => 789, 'name' => 'Role Permissions - View', 'module_id' => 11),
        ));
    }
}
