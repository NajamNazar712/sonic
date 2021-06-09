<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForCancellAddShipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 505, 'name' => 'Add - Cancel Shipments', 'module_id' => 15),
        ));
    }
}
