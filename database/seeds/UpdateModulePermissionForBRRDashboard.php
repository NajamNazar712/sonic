<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForBRRDashboard extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 313, 'name' => 'Screen 1', 'module_id' => 2),
            array('id' => 314, 'name' => 'Screen 2', 'module_id' => 2),
            array('id' => 315, 'name' => 'Sales Dashboard', 'module_id' => 2),
        ));
    }
}
