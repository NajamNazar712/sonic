<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForNsaAccountSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 367, 'name' => 'NSA Accounts', 'module_id' => 14)
        ));
    }
}
