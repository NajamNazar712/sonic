<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForIntlSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 581, 'name' => 'DHL Sync Time', 'module_id' => 14),
            array('id' => 582, 'name' => 'International Automation User', 'module_id' => 14)
        ));
    }
}
