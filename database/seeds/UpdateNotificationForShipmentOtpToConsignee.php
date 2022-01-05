<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForShipmentOtpToConsignee extends Seeder
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
            array('id' => 165, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Shipment Refusal Otp', 'type_id' => 2, 'subject' => null, 'body' => 'Dear [consignee]' . PHP_EOL . 'Please inform the OTP [otp] to rider for the refusal of Shipment [tracking_no].', 'updated_by' => 7, 'status' => 1),
        ));
    }
}
