<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableDebriefingReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 170, 'name' => 'Debriefing', 'module_id' => 9)
        ));
    }
}
