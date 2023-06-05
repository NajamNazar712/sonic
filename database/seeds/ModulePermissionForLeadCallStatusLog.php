<?php

use Illuminate\Database\Seeder;

class ModulePermissionForLeadCallStatusLog extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 870, 'name' => 'Leads Call Status (Make A Call) - Action', 'module_id' => 25),
            array('id' => 871, 'name' => 'Leads Call Status (End A Call) - Action', 'module_id' => 25),
        ));

     

    }
}
