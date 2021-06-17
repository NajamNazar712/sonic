<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForCrmEscalationLaunchedSettingAction extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 507, 'name' => 'CRM Escalation - Launched - Edit', 'module_id' => 14)
        ));
    }
}
