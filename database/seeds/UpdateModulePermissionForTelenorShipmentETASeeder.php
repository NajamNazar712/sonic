<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForTelenorShipmentETASeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 534, 'name' => 'Telenor Shipment Status ETA', 'module_id' => 14),
        ));
    }
}
