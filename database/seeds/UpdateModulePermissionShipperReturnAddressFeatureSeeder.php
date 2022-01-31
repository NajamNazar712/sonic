<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionShipperReturnAddressFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 668, 'name' => 'Shipper Return Address', 'module_id' => 14),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Shipper > Shipper Return Address', 'url'=>'admin.settings.shippers_return_address.index', 'permission_id' => 668),
        ));
    }
}
