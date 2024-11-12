<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BulkShipmentPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Update bulk shipments and individual shipment
        // DB::table('module_permissions')->insert(array(
        //     array('id' => 1014, 'name' => 'Bulk Shipment Update and Individual Shipment Update', 'module_id' => 8)
        // ));

        // View bulk shipments 
        DB::table('module_permissions')->insert(array(
            array('id' => 1015, 'name' => 'Bulk Shipment View', 'module_id' => 8)
        ));
    }
}
