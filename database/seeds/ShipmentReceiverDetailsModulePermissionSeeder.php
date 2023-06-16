<?php

use Illuminate\Database\Seeder;

class ShipmentReceiverDetailsModulePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 878, 'name' => 'Shipment Receiver Details - View', 'module_id' => 33),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 669, 'screen_name' => 'Shipment Receiver Details', 'action'=> 'View'),
            array('id' => 670, 'screen_name' => 'Shipment Receiver Details', 'action'=> 'Add'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support > Shipment Receiver Details', 'url'=>'admin.management.shipment_received.index', 'permission_id' => 878),
        ));
    }
}
