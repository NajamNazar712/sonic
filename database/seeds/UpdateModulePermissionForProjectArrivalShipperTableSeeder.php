<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForProjectArrivalShipperTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 828, 'name' => 'Settings - Project Arrival Shippers', 'module_id' => 14),
            array('id' => 830, 'name' => 'Project Shipper Arrival of Shipments', 'module_id' => 3),
        ));
    }
}
