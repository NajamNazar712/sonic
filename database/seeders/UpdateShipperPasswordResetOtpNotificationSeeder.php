<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateShipperPasswordResetOtpNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('notifications')->insert([
            [
                'id' => 248,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Shipper App password reset OTP',
                'type_id' => 2,
                'subject' => null,
                'body' => 'Dear [user_name], your password reset code for the Shipper App is [otp]. It will expire in [expire_at] minutes. For support, contact Trax.',
                'updated_by' => 3495,
                'status' => 0
            ]
        ]);
    }
}
