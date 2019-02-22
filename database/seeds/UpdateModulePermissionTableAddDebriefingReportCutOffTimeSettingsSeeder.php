<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableAddDebriefingReportCutOffTimeSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
			array('id' => 175, 'name' => 'Debriefing Report Cut-Off Time', 'module_id' => 14)
		));
    }
}
