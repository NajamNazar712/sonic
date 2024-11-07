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
            array('id' => 452, 'name' => 'Fuel Management History- View', 'module_id' => 11),
            array('id' => 451, 'name' => 'Fuel Management- Add', 'module_id' => 11),
            array('id' => 450, 'name' => 'Fuel Management- Edit', 'module_id' => 11),
            array('id' => 448, 'name' => 'Fuel Management- Approve', 'module_id' => 11),
        ));
    }
}
