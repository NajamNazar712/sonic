<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateModulePermissionForOTPRefusalShippers extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 826, 'name' => 'Consignee Refusal OTP Bypass', 'module_id' => 14),
            array('id' => 827, 'name' => 'OTP History', 'module_id' => 6),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 623, 'screen_name' => 'Consignee Refusal OTP Bypass', 'action'=> 'View'),
            array('id' => 624, 'screen_name' => 'Consignee Refusal OTP Bypass', 'action'=> 'Submit'),
            array('id' => 625, 'screen_name' => 'OTP History', 'action'=> 'View'),
            array('id' => 626, 'screen_name' => 'OTP History', 'action'=> 'Excel'),
        ));

        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Shippers > Consignee Refusal OTP Bypass', 'url'=>'admin.settings.consignee_refused_otp_bypass.index', 'permission_id' => 826),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support > OTP History', 'url'=>'admin.otp_history.index', 'permission_id' => 827)
        );
    }
}
