<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForDeliveredToShipperTableSeeder extends Seeder
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
            array('id' => 39, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Return Delivered To Shipper', 'type_id' => 1, 'subject' => 'Shipment Return Delivered to Shipper [tracking_number]', 'body' => 'Tracking ID [tracking_number] has been returned to you on [status_updated_at] and received by [receiver_name]', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
