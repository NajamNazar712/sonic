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
            array('id' => 226, 'name' => 'Packaging - Confirm', 'module_id' => 10),
            array('id' => 227, 'name' => 'Packaging - Cancel', 'module_id' => 10),
            array('id' => 228, 'name' => 'Stock Movement Account - View ', 'module_id' => 10),
        ));
        DB::table('module_permissions')->where('id', 80)->update([
            'name' => 'Packaging - Dispatch'
        ]);
    }
}
