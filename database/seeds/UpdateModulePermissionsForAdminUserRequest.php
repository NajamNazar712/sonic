<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionsForAdminUserRequest extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 394, 'name' => 'Admin User Request - View', 'module_id' => 11)
        ));
    }
}
