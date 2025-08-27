<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionForRetailShipperOtpScreen extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('module_permissions')->insert([
            ['id' => 1043, 'name' => 'Retail Shipper OTP', 'module_id' => 33],
        ]);

        // if new screen or excel
        DB::table('activity_trail_actions')->insert([
            ['id' => 833, 'screen_name' => 'Retail Shipper OTP', 'action' => 'Screen'],
            ['id' => 834, 'screen_name' => 'Retail Shipper OTP Excel', 'action' => 'Excel'],
        ]);

        // if new screen
        DB::table('admins_screen_list')->insert([
            [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Support > Retail Shipper OTP',
                'url' => 'admin.retail_otp.index',
                'permission_id' => 1043
            ],
        ]);

    }
}
