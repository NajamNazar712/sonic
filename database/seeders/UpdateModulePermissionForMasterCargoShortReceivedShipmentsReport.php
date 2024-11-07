<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForMasterCargoShortReceivedShipmentsReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 476, 'name' => 'Master Cargo Short Received Shipments - View', 'module_id' => 9),
        ));
        DB::table('module_permissions')->where('id',319)->where('module_id',9)->update(['name' => 'Cargo Short Received Shipments Report - View']);
        
    }
}
