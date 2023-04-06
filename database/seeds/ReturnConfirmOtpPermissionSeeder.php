<?php

use Illuminate\Database\Seeder;

class ReturnConfirmOtpPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        DB::table('module_permissions')->insert(array(
            array('id' => 849, 'name' => 'Return Confirm OTP - View', 'module_id' => 7),
        ));


        DB::table('activity_trail_actions')->insert(array(
            array('id' => 640, 'screen_name' => 'Return Confirm OTP', 'action'=> 'View'),
            array('id' => 641, 'screen_name' => 'Return Confirm OTP ', 'action'=> 'Excel Download'),
        ));


        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp,
             'name' => 'Last Mile > Return > Return Confirm OTP', 
             'url'=>'admin.return.return_confirm_otp.index', 'permission_id' => 849),
        ));
    }
}
