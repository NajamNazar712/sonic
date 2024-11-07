<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForRiderResetPinOtpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        DB::table('notifications')->insert(array(
            array('id' => 158, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Rider Reset Pin OTP', 'type_id' => 2, 'subject' => null, 'body' => 'Dear [rider_name],' . PHP_EOL . 'Your OTP for Reset Pin of Bolt is: [otp]', 'updated_by' => 7, 'status' => 0)
        ));
    }
}
