<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForNonCodShipperOtpScreenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        DB::table('global_settings')->insert(array(
            array('type' => 'non_cod_otp_excluded_shippers', 'setting_value' => 0, 'text' => '', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('type' => 'non_cod_otp_only_shippers', 'setting_value' => 0, 'text' => '', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('type' => 'non_cod_otp_all_shippers', 'setting_value' => 1, 'text' => '', 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 613, 'screen_name' => 'Non-Cod OTP Shippers Setting', 'action'=> 'View'),
            array('id' => 614, 'screen_name' => 'Non-Cod OTP Shippers Setting', 'action'=> 'Update')
        ));

        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Shippers > Non-Cod OTP Shippers Setting', 'url'=>'admin.settings.non_cod_otp_shippers.index', 'permission_id' => 820)
        ));

        DB::table('module_permissions')->insert(array(
            array('id' => 820, 'name' => 'Non-Cod OTP Shippers Setting - View', 'module_id' => 14),
        ));
    }
}
