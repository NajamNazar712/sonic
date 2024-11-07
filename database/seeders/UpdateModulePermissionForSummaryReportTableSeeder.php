<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForSummaryReportTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 210, 'name' => 'Summary Report', 'module_id' => 9),
        ));
    }
}
