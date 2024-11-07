<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UpdateNotificationForConsigneeZeroCodOtp extends Seeder
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
            array('id' => 192, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Consignee Zero Cod Otp', 'type_id' => 2, 'subject' => null, 'body' => 'Dear [consignee_name]' . PHP_EOL . 'Trax rider [rider_name] generated an OTP for Shipment [tracking_number].' . PHP_EOL . 'OTP : [otp].' , 'updated_by' => 7, 'status' => 1),
        ));
    }
}
