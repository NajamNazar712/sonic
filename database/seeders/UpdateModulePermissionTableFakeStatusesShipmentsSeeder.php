<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableFakeStatusesShipmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 262, 'name' => 'Add Fake Status - View', 'module_id' => 6),
            array('id' => 263, 'name' => 'Fake Statuses Shipments - View', 'module_id' => 9)
        ));
    }
}
