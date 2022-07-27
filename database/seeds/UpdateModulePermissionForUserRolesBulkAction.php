<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForUserRolesBulkAction extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 775, 'name' => 'Roles - Bulk Add', 'module_id' => 11),
            array('id' => 776, 'name' => 'Roles - Bulk Remove', 'module_id' => 11)
        ));
    }
}
