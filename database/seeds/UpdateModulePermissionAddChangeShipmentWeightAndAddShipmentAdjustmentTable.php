<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionAddChangeShipmentWeightAndAddShipmentAdjustmentTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 133, 'name' => 'Change Shipment Weight - View', 'module_id' => 8),
            array('id' => 134, 'name' => 'Change Shipment Weight - Change', 'module_id' => 8),
            array('id' => 135, 'name' => 'Add Shipment Adjustment - View', 'module_id' => 8),
            array('id' => 136, 'name' => 'Add Shipment Adjustment - Add', 'module_id' => 8)
        ));
    }
}
