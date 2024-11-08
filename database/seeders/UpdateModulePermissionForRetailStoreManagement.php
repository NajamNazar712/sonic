<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateModulePermissionForRetailStoreManagement extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 431, 'name' => 'Trax Center - View', 'module_id' => 26),
            array('id' => 432, 'name' => 'Franchise  - View', 'module_id' => 26),
            array('id' => 433, 'name' => 'Add Trax Center - Add', 'module_id' => 26),
            array('id' => 434, 'name' => 'Add Franchise  - Add', 'module_id' => 26),
            array('id' => 435, 'name' => ' Trax Center  - Action', 'module_id' => 26),
            array('id' => 436, 'name' => 'Franchise  - Action', 'module_id' => 26),
        ));
    }
}
