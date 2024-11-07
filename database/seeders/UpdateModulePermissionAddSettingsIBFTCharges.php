<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionAddSettingsIBFTCharges extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 230, 'name' => 'IBFT Charges', 'module_id' => 14)
        ));
    }
}
