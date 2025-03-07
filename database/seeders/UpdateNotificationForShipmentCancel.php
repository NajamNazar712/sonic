<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateNotificationForShipmentCancel extends Seeder
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
            array('id' => 62, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Cancelled Shipments', 'type_id' => 1, 'subject' => 'Booking Cancelled', 'body' => 'Dear Shipper,' . PHP_EOL . PHP_EOL .'It is to notify you that below listed shipments have been cancelled as these shipments are  still not provided for dispatch. Please contact 021-38772222 in case you have already handed over any of these shipments.' . PHP_EOL . PHP_EOL .'Regards,'.PHP_EOL.'Team Trax' . PHP_EOL . PHP_EOL .'[cancel_shipment]', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
