<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForCrmEscalationInProcessSettingAction extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 517, 'name' => 'CRM Escalation - In Process - Edit', 'module_id' => 14)
        ));
    }
}
