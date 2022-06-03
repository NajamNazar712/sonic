<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForInvoiceReturnDeliveredShipper extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 716, 'name' => 'Invoice Return Delivered Shipper - View', 'module_id' => 14),
        ));
    }
}
