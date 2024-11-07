<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForEmployeeShifts extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 592, 'name' => 'Employee Shift - View', 'module_id' => 28),
            array('id' => 593, 'name' => 'Employee Shift - Add/Edit', 'module_id' => 28),
            array('id' => 594, 'name' => 'Employee Shift - Enable/Disable', 'module_id' => 28)
        ));
    }
}
