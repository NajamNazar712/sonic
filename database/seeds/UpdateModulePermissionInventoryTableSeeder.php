<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionInventoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 221, 'name' => 'Inventory - View', 'module_id' => 10),
            array('id' => 222, 'name' => 'Stock - Request', 'module_id' => 10),
            array('id' => 223, 'name' => 'Stock - Confirm', 'module_id' => 10),
            array('id' => 224, 'name' => 'Stock - Dispatch', 'module_id' => 10),
            array('id' => 225, 'name' => 'Stock - Cancel', 'module_id' => 10),
        ));
    }
}
