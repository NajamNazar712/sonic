<?php

namespace Database\Seeders;
use App\Http\Models\Notification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
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

        $check = Notification::find(243);
        if(!$check) {
            DB::table('notifications')->insert([
                [
                    'id' => 243,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                    'name' => 'Retail Shipper App registration OTP',
                    'type_id' => 2,
                    'subject' => null,
                    'body' => 'Dear [user_name], your code for Trax Retail Shipper App is [otp]. It will expire in [expire_at] minutes. For help, contact Trax support.',
                    'updated_by' => 3495,
                    'status' => 0
                ]
            ]);
        }

    }
}
