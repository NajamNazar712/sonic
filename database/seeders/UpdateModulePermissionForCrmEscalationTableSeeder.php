<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForCrmEscalationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 347, 'name' => 'CRM Escalation - Launched', 'module_id' => 14),
            array('id' => 348, 'name' => 'CRM Escalation - In-Process', 'module_id' => 14),
            array('id' => 349, 'name' => 'CRM Escalation - Levels', 'module_id' => 14),
            array('id' => 350, 'name' => 'CRM Escalation - Tagging', 'module_id' => 14),
            array('id' => 351, 'name' => 'CRM - Default Agent', 'module_id' => 14),
            array('id' => 352, 'name' => 'Request Details - Halt/Start Escalation', 'module_id' => 18),
            array('id' => 353, 'name' => 'Request Details - Escalate', 'module_id' => 18),
        ));
    }
}
