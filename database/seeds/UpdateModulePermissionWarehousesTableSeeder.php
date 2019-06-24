<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionWarehousesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 217, 'name' => 'Warehouse - View', 'module_id' => 10),
            array('id' => 218, 'name' => 'Warehouse - Enable/Disable', 'module_id' => 10),
            array('id' => 219, 'name' => 'Warehouse - Add/Edit', 'module_id' => 10),
        ));
    }
}
