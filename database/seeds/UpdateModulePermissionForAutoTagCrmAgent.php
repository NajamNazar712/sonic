<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForAutoTagCrmAgent extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 639, 'name' => 'CRM - Auto Tagging', 'module_id' => 14),
            array('id' => 640, 'name' => 'CRM - Auto Tagging - ADD/EDIT', 'module_id' => 14),
        ));
    }
}
