<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForLeadManagementTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 416, 'name' => 'Lead - View', 'module_id' => 25),
        ));
    }
}
