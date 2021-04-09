<?php

use Illuminate\Database\Seeder;

class UpdateNameForCargoShortReceivedShipmentsReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('module_permissions')->where('name','Cargo Short Received Shipments Report - View')->update(['name' => 'Master Cargo Short Received Shipments Report - View']);
    }
}
