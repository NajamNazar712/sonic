<?php

use Illuminate\Database\Seeder;

class UpdateAdminsScreenForRiderDeliveryNoteOtpScreen extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support  >  Rider Delivery Note OTP', 'url'=>'admin.rider_delivery_note_otp.index', 'permission_id' => 553));
    }
}
