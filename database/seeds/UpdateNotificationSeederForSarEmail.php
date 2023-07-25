<?php

use Illuminate\Database\Seeder;

class UpdateNotificationSeederForSarEmail extends Seeder
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
             array('id' => 220, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Shipper Advise Requested Notification', 'type_id' => 1, 
            'subject' => 'Action Required: Shipper Advise Requested - Shipment Update', 
            'body' => 'Dear [company_name],' .PHP_EOL.
            'We hope this message finds you well. This is a friendly reminder about the "Shipper Advise Requested" status of some of your shipments on SONIC. Please take a moment to review and advise on the next course of action for these shipments within 24 hours from the time of this notification. If no response is received within the specified time frame, the affected shipment(s) will be automatically updated to "Return Confirm" status.' . PHP_EOL . PHP_EOL .
            'Please note that you will receive this email every 4 hours until a response is received or the shipments are automatically updated.' . PHP_EOL . PHP_EOL .
            'Your prompt action is greatly appreciated.' . PHP_EOL . PHP_EOL .
            'Best regards,',
            'updated_by' => 7, 'status' => 1)
         ));
    }
}
