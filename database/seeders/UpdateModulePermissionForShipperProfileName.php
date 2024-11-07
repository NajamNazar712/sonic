<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForShipperProfileName extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 250, 'name' => 'Shipper Profile Name - Edit', 'module_id' => 2),
        ));
    }
}
