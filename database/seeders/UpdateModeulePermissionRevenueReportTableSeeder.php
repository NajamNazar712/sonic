<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateModulePermissionRevenueReportTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 177, 'name' => 'Revenue', 'module_id' => 9),
        ));
    }
}
