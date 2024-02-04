<?php

use Illuminate\Database\Seeder;

class UpdateRvReportForPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 905, 'name' => 'RV Report - View', 'module_id' => 9),
        ));
    }
}
