<?php

use Illuminate\Database\Seeder;

class ModulePermissionForReturnShipments extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 860, 'name' => 'Return Shipments', 'module_id' => 7),
        )); 
    }
}
