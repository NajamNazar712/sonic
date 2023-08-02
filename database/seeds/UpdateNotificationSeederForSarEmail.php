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
            'body' => 'Dear Valued Customer,
            Thank you for using Trax Services,
            This is to inform you that below shipment(s) are undelivered. You are requested to advise next course of action or disposal for the undelivered shipments through your portal by using below link within 24 hours of this notification.
            https://sonic.pk/cod/tracking
            No response within the specified time, the shipment will be automatically processed as return.'. PHP_EOL .'[preview]'.'<br></br>Thankyou for your cooperation',
            'updated_by' => 7, 'status' => 1)
         ));
    }
}
