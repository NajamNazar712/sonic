<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableCargoReturnsShipment extends Seeder
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
            array('id' => 172, 'name' => 'Cargo Returns Shipment', 'module_id' => 9)
        ));
    }
}
