<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForKeyAccountDashboardTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modules')->insert(array(
            array('id' => 23, 'name' => 'Sales'),
        ));
        DB::table('module_permissions')->insert(array(
            array('id' => 392, 'name' => 'Key Accounts Dashboard - View', 'module_id' => 23),
        ));
    }
}
