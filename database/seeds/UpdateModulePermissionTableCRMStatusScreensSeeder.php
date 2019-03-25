<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableCRMStatusScreensSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 179, 'name' => 'Launched/Re-Open Requests', 'module_id' => 18),
            array('id' => 180, 'name' => 'In-Process Requests', 'module_id' => 18),
            array('id' => 181, 'name' => 'Resolved Requests', 'module_id' => 18),
            array('id' => 182, 'name' => 'Closed Requests', 'module_id' => 18)
        ));
    }
}
