<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
class UpdateNotificationsForOTPVerificationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('notifications')->insert(array(
            array('id' => 114, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Shipper OTP Verification', 'type_id' => 2, 'subject' => NULL, 'body' => 'Dear Shipper,' . PHP_EOL . 'Your OTP verification code is [code]. This will expire in 15 days', 'updated_by' => 6, 'status' => 0)
        ));
    }
}
