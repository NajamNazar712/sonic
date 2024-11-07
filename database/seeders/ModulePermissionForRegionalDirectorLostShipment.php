<?php

use Illuminate\Database\Seeder;

class ModulePermissionForRegionalDirectorLostShipment extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 944, 'name' => 'Regional Director - Permission', 'module_id' => 6),
        ));
    }
}
