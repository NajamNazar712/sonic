<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateNotificationForRetailOtpTableSeeder extends Seeder
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
            array('id' => 129, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Retail Login OTP verification', 'type_id' => 2, 'subject' => '', 'body' => 'Dear [name], Your OTP verification code is [code]', 'updated_by' => 7, 'status' => 1),
        ));
    }
}
