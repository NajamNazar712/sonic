<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionCrmSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 237, 'name' => 'Crm Setting - View', 'module_id' => 14),
        ));
    }
}
