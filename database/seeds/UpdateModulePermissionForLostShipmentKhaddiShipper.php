<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForLostShipmentKhaddiShipper extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 708, 'name' => 'Lost Shipment Shipper - View', 'module_id' => 14),
            array('id' => 709, 'name' => 'Lost Shipment Shipper - Add / Delete', 'module_id' => 14),
            array('id' => 710, 'name' => 'Lost Shipment Admin - View', 'module_id' => 14),
            array('id' => 711, 'name' => 'Lost Shipment Admin - Add / Delete', 'module_id' => 14),
        ));
    }
}
