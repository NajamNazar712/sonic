<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableForSprint37Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 304, 'name' => 'Receive - Re-Assign Rider', 'module_id' => 6),
            array('id' => 305, 'name' => 'In Transit - Send at Link', 'module_id' => 4),
            array('id' => 306, 'name' => 'Shipment Scanning History', 'module_id' => 19)
        ));
    }
}
