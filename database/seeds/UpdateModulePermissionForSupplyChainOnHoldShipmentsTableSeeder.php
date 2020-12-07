<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForSupplyChainOnHoldShipmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 404, 'name' => 'On-Hold - Update', 'module_id' => 24),
            array('id' => 405, 'name' => 'On-Hold - History', 'module_id' => 24),
        ));
    }
}
