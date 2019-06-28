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
        ));
    }
}
