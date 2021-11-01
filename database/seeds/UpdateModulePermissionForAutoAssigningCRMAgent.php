<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForAutoAssigningCRMAgent extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 616, 'name' => 'CRM - Auto Assigning', 'module_id' => 14),
            array('id' => 617, 'name' => 'CRM - Auto Assigning - ADD', 'module_id' => 14),
            array('id' => 618, 'name' => 'CRM - Auto Assigning - EDIT/DELETE', 'module_id' => 14),
        ));
    }
}
