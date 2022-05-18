<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForLostShipmentKhaddiShipper extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support > Lost > Lost Shipment Shippers', 'url'=>'admin.settings.lost_shipment_shippers.index', 'permission_id' => 708),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support > Lost > Lost Shipment Admins', 'url'=>'admin.settings.lost_shipment_admins.index', 'permission_id' => 710),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 527, 'screen_name' => 'Lost Shipment Shippers', 'action'=> 'View'),
            array('id' => 528, 'screen_name' => 'Lost Shipment Admins', 'action'=> 'View'),
        ));

        DB::table('module_permissions')->insert(array(
            array('id' => 708, 'name' => 'Lost Shipment Shipper - View', 'module_id' => 14),
            array('id' => 709, 'name' => 'Lost Shipment Shipper - Add / Delete', 'module_id' => 14),
            array('id' => 710, 'name' => 'Lost Shipment Admin - View', 'module_id' => 14),
            array('id' => 711, 'name' => 'Lost Shipment Admin - Add / Delete', 'module_id' => 14),
        ));
    }
}
