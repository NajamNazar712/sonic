<?php

use Illuminate\Database\Seeder;

class ModulePermissionForDeliveryShipments extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 859, 'name' => 'Delivery Shipments', 'module_id' => 6),
        )); 
    }
}
