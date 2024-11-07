<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForBulkDeliverReturnCarrefour extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 606, 'name' => 'Carrefour Bulk Delivery - View', 'module_id' => 6),
            array('id' => 607, 'name' => 'Carrefour Bulk Return - View', 'module_id' => 7),
        ));
    }
}
