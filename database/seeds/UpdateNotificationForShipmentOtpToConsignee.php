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
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('notifications')->insert(array(
            array('id' => 165, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Shipment Refusal Otp', 'type_id' => 2, 'subject' => null, 'body' => 'Dear customer' . PHP_EOL . 'Trax Rider is on the way.' . PHP_EOL . 'Please Keep your CNIC ready.' . PHP_EOL . 'For verification purpose and record keeping, rider will ask for your CNIC' . PHP_EOL . 'Thank you' . PHP_EOL . 'AWB#[tracking_number].' . PHP_EOL . 'Rider:[rider]' . PHP_EOL . '021-111-118-729', 'updated_by' => 7, 'status' => 1),
        ));
    }
}
