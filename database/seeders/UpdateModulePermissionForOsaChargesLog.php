<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForOsaChargesLog extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
           array('id' => 647, 'name' => 'OSA Charges Log - View', 'module_id' => 9),
        ));
    }
}
