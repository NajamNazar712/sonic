<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableShipmentConsolidationHisrtorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 254, 'name' => 'Consolidation History - View', 'module_id' => 17)
        ));
    }
}
