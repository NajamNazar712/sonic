<?php

use Illuminate\Database\Seeder;

class UpdateNotificaitonForShipmentStatusWebhookSeeder extends Seeder
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
            array('id' => 167, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Shipment Status Webhook Subscription', 'type_id' => 1, 'subject' => 'Shipment Status Webhook Subscription', 'body' => 'Dear Concern,' . PHP_EOL . 'This is an automated email to inform you that Shipment Status Webhook Subscription Failed for the following link: [link] with Status Code: [status_code] and message : [message]', 'updated_by' => 6, 'status' => 0),
        ));
    }
}
