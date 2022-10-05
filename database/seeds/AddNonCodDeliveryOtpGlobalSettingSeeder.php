<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AddNonCodDeliveryOtpGlobalSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('global_settings')->insert(array(
            array('type' => 'delivery_otp', 'setting_value' => 1, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));

        DB::table('module_permissions')->insert(array(
            array('id' => 819, 'name' => 'Non-COD Shipment OTP - Update', 'module_id' => 6)
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 612, 'screen_name' => 'Non-COD Shipment OTP', 'action'=> 'Update')
        ));
    }
}
