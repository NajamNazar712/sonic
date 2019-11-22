<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableForDeliveredShipmentReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 275, 'name' => 'Delivered Shipment', 'module_id' => 9)
        ));
    }
}
