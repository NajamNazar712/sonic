<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForReturnConfirmAgentProductivity extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 600, 'name' => 'RCP Agent Productivity', 'module_id' => 7),
        ));
    }
}
