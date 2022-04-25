<?php

use App\Http\Models\Notification;
use Illuminate\Database\Seeder;

class UpdateNotificationForDonePaymentSmsBodySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $notification = Notification::find(172);
        $notification->body = 'Dear  [shipper_name],' . PHP_EOL . 'Your shipment has been paid by TRAX Logistics at [updated_at] of amount [total_amount].You can view your payment details by following the link below [link]'. PHP_EOL .'Thank you for being our valued customer.';
        $notification->save();
    }
}