<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableSeederRequestsScreens extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 233, 'name' => 'Launched/Re-Open', 'module_id' => 18),
            array('id' => 234, 'name' => 'In-Process', 'module_id' => 18),
            array('id' => 235, 'name' => 'Resolved', 'module_id' => 18),
            array('id' => 236, 'name' => 'Closed', 'module_id' => 18),
        ));
    }
}
