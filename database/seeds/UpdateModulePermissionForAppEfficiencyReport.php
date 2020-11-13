<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForAppEfficiencyReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 401, 'name' => 'App Efficiency Report - View', 'module_id' => 9),
        ));
    }
}
