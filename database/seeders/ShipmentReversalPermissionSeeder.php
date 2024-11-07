<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShipmentReversalPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 1001, 'name' => 'Shipment Reversal Report - View', 'module_id' => 9),
        ));
    }
}
