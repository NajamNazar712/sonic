<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForIntlShipmentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 508, 'name' => 'International Shipment Status - View', 'module_id' => 17),
        ));
    }
}
