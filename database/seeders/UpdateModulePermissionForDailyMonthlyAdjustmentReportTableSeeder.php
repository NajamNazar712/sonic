<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateModulePermissionForDailyMonthlyAdjustmentReportTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 373, 'name' => 'Daily/Monthly Adjustment', 'module_id' => 9)
        ));
    }
}
