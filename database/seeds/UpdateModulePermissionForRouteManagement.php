<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForRouteManagement extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 499, 'name' => 'Route Management - View', 'module_id' => 14),
            array('id' => 504, 'name' => 'Route Management - Add', 'module_id' => 14)
        ));
    }
}
