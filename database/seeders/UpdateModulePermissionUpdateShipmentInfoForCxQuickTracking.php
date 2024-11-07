<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionUpdateShipmentInfoForCxQuickTracking extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('module_permissions')->insert(array(
            array('id' => 247, 'name' => 'Update Shipment Info', 'module_id' => 19)
        ));
    }
}
