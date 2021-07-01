<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForDeliveryNoteOtpSeeder extends Seeder
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
            array('id' => 137, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Delivery Note Otp', 'type_id' => 2, 'subject' => null, 'body' => 'Dear Rider [rider_name]' . PHP_EOL . 'Your Otp for Delivery Note # [delivery_note] is [otp].', 'updated_by' => 7, 'status' => 1)
        ));
    }
}
