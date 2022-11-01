<?php

use Illuminate\Database\Seeder;

class UpdatePermissionSeederForZeroCodShipmentOtp extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 608, 'screen_name' => 'Non-COD Shipment OTP', 'action'=> 'View'),
            array('id' => 609, 'screen_name' => 'Non-COD Shipment OTP', 'action'=> 'Excel Download')
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile > Delivery > Non-COD Shipment OTP', 'url'=>'admin.shipment_otp.index', 'permission_id' => 815)
        ));

        DB::table('module_permissions')->insert(array(
            array('id' => 815, 'name' => 'Non-COD Shipment OTP - View', 'module_id' => 6),
        ));
    }
}
