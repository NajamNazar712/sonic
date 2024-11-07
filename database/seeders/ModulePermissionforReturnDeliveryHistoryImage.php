<?php

use Illuminate\Database\Seeder;

class ModulePermissionforReturnDeliveryHistoryImage extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 608, 'name' => 'Return Delivery History Image - Delete', 'module_id' => 7)
        ));
    }
}
