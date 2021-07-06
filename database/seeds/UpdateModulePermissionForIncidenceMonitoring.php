<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForIncidenceMonitoring extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 535, 'name' => 'Incidence Monitoring - View', 'module_id' => 31),
            array('id' => 536, 'name' => 'Incidence Monitoring - Action', 'module_id' => 31),
        ));
    }
}
