<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionAddZonalManagementTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 131, 'name' => 'Zonal Management - View', 'module_id' => 12),
            array('id' => 132, 'name' => 'Zonal Management - Add', 'module_id' => 12),
            array('id' => 133, 'name' => 'Zonal Management - Update', 'module_id' => 12)
        ));
    }
}
