<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForCallMonitoringModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modules')->insert(array(
            array('id' => 29, 'name' => 'Debriefing')
        ));

        DB::table('module_permissions')->insert(array(
            array('id' => 495, 'name' => 'Supervisor Dashboard - View', 'module_id' => 29),
            array('id' => 496, 'name' => 'Agent Call Monitoring - View', 'module_id' => 29),
        ));
    }
}
