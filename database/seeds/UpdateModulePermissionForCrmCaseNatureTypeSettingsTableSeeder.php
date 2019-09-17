<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForCrmCaseNatureTypeSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 260, 'name' => 'Add Case Nature Types', 'module_id' => 14)
        ));
    }
}
