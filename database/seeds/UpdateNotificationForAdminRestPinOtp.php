<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForAdminRestPinOtp extends Seeder
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
            array('id' => 162, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Admin Reset Pin OTP', 'type_id' => 2, 'subject' => null, 'body' => 'Dear [user_name],' . PHP_EOL . 'Your OTP for Reset Pin of Bolt is: [otp]', 'updated_by' => 7, 'status' => 0)
        ));
    }
}
