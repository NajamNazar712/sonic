<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForOvernightOverlandReportSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 311, 'name' => 'Overnight Overland Report', 'module_id' => 14)
        ));
    }
}
