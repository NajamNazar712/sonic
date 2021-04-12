<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionNameForCargoShortReceivedShipmentsReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->where('id',319)->where('module_id',9)->update(['name' => 'Master Cargo Short Received Shipments Report - View']);
    }
}
