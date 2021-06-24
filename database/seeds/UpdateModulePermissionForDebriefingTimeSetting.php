<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForDebriefingTimeSetting extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 526, 'name' => 'Debriefing Time Setting', 'module_id' => 14),
        ));
    }
}
