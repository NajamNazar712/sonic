<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForAdminOTPTableSeeder extends Seeder
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
            array('id' => 138, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Admin Login OTP verification', 'type_id' => 2, 'subject' => '', 'body' => 'Dear [name], Your OTP verification code is [code]', 'updated_by' => 7, 'status' => 1),
        ));
    }
}
