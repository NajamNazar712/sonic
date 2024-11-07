<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForDwsReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 642, 'name' => 'DWS Report', 'module_id' => 9)
        ));
    }
}
