<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForFTLCharges extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 552, 'name' => 'FTL - Other Charges', 'module_id' => 30),
            array('id' => 553, 'name' => 'FTL - Freight Charges', 'module_id' => 30),
        ));
    }
}
