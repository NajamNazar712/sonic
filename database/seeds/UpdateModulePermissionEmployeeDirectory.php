<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionEmployeeDirectory extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 467, 'name' => 'Employee Directory - View', 'module_id' => 11),
            array('id' => 468, 'name' => 'Employee Directory - Update', 'module_id' => 11),
            array('id' => 469, 'name' => 'Employee Directory - Approve/Reject', 'module_id' => 11),
        ));
    }
}
