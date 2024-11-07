<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableForCRMReopenSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 274, 'name' => 'CRM Re-Open - View', 'module_id' => 14)
        ));
    }
}
