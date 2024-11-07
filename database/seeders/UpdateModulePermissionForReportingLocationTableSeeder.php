<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForReportingLocationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
        array('id' => 478, 'name' => 'Reporting Location - View', 'module_id' => 11),
        array('id' => 479, 'name' => 'Reporting Location - Add/Edit', 'module_id' => 11),
        array('id' => 480, 'name' => 'Reporting Location - Enable/Disable', 'module_id' => 11),
        array('id' => 481, 'name' => 'Designation - View', 'module_id' => 11),
        array('id' => 482, 'name' => 'Designation - Add/Edit', 'module_id' => 11),
        array('id' => 483, 'name' => 'Designation - Enable/Disable', 'module_id' => 11),
        array('id' => 484, 'name' => 'Department - View', 'module_id' => 11),
        array('id' => 485, 'name' => 'Department - Add/Edit', 'module_id' => 11),
    ));
    }
}
