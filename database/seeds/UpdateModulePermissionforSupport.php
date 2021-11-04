<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionforSupport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 621, 'name' => 'Order Management', 'module_id' => 15),
            array('id' => 622, 'name' => 'Self Collection Shipments', 'module_id' => 15)
        ));
    }
}
