<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionsForTerritoryTag extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 445, 'name' => 'Shipper Territory Tag', 'module_id' => 2),
        ));
    }
}
