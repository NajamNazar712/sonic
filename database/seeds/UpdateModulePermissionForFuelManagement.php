<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForFuelManagement extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 447, 'name' => 'Fuel Management - View', 'module_id' => 11),
        ));
    }
}
