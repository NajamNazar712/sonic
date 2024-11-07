<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForReturnShipmentAddressChangeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 689, 'name' => 'Return Address Change - View', 'module_id' => 14)
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Shipper > Return Address Change', 'url'=>'admin.settings.return_shipments_address.index', 'permission_id' => 689));

    }
}
