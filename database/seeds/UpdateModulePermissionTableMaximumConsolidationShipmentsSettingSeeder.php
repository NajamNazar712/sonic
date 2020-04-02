<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableMaximumConsolidationShipmentsSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('module_permissions')->insert(array(
            array('id' => 256, 'name' => 'Maximum Consolidation Shipments - View', 'module_id' => 14),
        ));
    }
}
