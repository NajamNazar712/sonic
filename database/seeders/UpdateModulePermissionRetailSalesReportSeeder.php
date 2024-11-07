<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionRetailSalesReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 493, 'name' => 'Retail Sales Report', 'module_id' => 9),
        ));
    }
}
