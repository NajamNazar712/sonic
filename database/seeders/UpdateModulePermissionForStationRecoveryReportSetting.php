<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateModulePermissionForStationRecoveryReportSetting extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 355, 'name' => 'Station Recovery Report Cron', 'module_id' => 14),
            array('id' => 356, 'name' => 'Station Recovery Report', 'module_id' => 9),
        ));
    }
}
