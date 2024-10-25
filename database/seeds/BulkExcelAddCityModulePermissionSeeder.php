<?php

use Illuminate\Database\Seeder;

class BulkExcelAddCityModulePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('module_permissions')->insert(array(
            array('id' => 1016, 'name' => 'Add Excel City Bulk - View', 'module_id' => 12),
        ));

    }
}
