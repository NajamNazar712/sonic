<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForAdjustmentsReportTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('module_permissions')->insert(array(
            array('id' => 248, 'name' => 'Adjustments Report - View', 'module_id' => 9),
        ));
    }
}
