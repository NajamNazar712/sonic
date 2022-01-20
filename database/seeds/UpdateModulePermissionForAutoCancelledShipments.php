<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForAutoCancelledShipments extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 660, 'name' => 'Auto Shipment Cancellation Settings', 'module_id' => 14)
        ));
    }
}
