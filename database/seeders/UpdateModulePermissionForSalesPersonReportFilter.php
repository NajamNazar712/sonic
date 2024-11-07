<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForSalesPersonReportFilter extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 261, 'name' => 'Overall Sales Person Filter', 'module_id' => 9)
        ));
    }
}
