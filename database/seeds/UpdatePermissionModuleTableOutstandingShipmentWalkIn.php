<?php

use Illuminate\Database\Seeder;

class UpdatePermissionModuleTableOutstandingShipmentWalkIn extends Seeder
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
            array('id' => 167, 'name' => 'Outstanding Walk-In Shipment', 'module_id' => 8)
        ));
    }
}
