<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionOrderManagementCancelButtonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 888, 'name' => 'Order Management (Cancel) - Action', 'module_id' => 33),
        ));
    }
}
