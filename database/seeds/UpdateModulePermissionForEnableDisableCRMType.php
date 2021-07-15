<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForEnableDisableCRMType extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 537, 'name' => 'CRM Case Nature Types - Action', 'module_id' => 14),
        ));
    }
}
