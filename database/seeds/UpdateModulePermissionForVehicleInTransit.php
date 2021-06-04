<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForVehicleInTransit extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 501, 'name' => 'Vehicle In Transit', 'module_id' => 4)
        ));
    }
}
