<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableShipperRecallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 139, 'name' => 'Order Management - Shipper Recall', 'module_id' => 7)
        ));
    }
}
