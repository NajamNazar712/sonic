<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForFleetManagement extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 498, 'name' => 'Fleet Management - View', 'module_id' => 14),
            array('id' => 503, 'name' => 'Fleet Management - Add', 'module_id' => 14)
        ));
    }
}
