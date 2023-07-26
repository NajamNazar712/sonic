<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionBulkLostShipmentExcelButton extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 890, 'name' => 'Add Lost Shipments (Excel Upload) - Excel', 'module_id' => 33),
        ));
    }
}
