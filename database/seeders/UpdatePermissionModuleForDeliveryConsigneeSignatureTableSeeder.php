<?php

use Illuminate\Database\Seeder;

class UpdatePermissionModuleForDeliveryConsigneeSignatureTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 441, 'name' => 'Delivery Consignee Signature - View', 'module_id' => '6')
        ));
    }
}
