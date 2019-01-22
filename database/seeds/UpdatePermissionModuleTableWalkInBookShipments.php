<?php

use Illuminate\Database\Seeder;

class UpdatePermissionModuleTableWalkInBookShipments extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('module_permissions')->insert(array(
            array('id' => 155, 'name' => 'Walk-In - View', 'module_id' => 15)
        ));
    }
}
