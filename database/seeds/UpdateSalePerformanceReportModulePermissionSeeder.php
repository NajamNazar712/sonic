<?php

use Illuminate\Database\Seeder;

class UpdateSalePerformanceReportModulePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 134, 'name' => 'Sales Person Performance', 'module_id' => 9),
        ));
    }
}
