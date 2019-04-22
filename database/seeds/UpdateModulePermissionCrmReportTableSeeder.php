<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionCrmReportTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 200, 'name' => 'CRM', 'module_id' => 9)
        ));
    }
}
