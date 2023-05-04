<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionDeBriefingRoleSettings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 858, 'name' => 'Debriefing Role Settings View', 'module_id' => 14),
        ));    }
}
