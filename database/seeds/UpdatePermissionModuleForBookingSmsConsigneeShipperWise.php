<?php

use Illuminate\Database\Seeder;

class UpdatePermissionModuleForBookingSmsConsigneeShipperWise extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 388, 'name' => 'Booking Sms For Consignee Shipper Wise', 'module_id' => 14)
        ));
    }
}
