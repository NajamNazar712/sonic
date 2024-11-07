<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForLastMileAppReportTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 437, 'name' => 'Last Mile App Report', 'module_id' => 9),
        ));
    }
}
