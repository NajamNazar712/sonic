<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateRetailShipperRegistrationOtpNotificationSeeder extends Seeder
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
                'id' => 243,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Retail Shipper App registration OTP',
                'type_id' => 2,
                'subject' => null,
                'body' => 'Dear [user_name],your OTP for Retail Shipper App registration is [otp]. It expires in [expire_at] minutes. Keep it confidential and do not share with anyone.',
                'updated_by' => 3495,
                'status' => 0
            ]
        ]);

    }
}
