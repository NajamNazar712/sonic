<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableDailyVisitReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 264, 'name' => 'Daily Visits', 'module_id' => 9)
        ));
    }
}
