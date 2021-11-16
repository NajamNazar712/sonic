<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForUndeliveredSmsToConsignee extends Seeder
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
            array('id' => 163, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'SMS to Consignee in case of Undelivered Shipment', 'type_id' => 2, 'subject' => null, 'body' => 'Dear Consignee' . PHP_EOL . 'Your order having tracking number [tracking_no] is [status] due to the reason being [reason]', 'updated_by' => 664, 'status' => 1)
        ));
    }
}
