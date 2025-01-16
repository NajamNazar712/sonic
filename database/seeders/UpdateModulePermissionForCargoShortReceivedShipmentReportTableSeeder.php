<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateModulePermissionForCargoShortReceivedShipmentReportTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 319, 'name' => 'Cargo Short Received Shipments Report - View', 'module_id' => 9)
        ));
    }
}
