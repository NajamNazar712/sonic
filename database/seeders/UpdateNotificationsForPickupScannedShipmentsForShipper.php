<?php

use Illuminate\Database\Seeder;

class UpdateNotificationsForPickupScannedShipmentsForShipper extends Seeder
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
            array('id' => 210, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Shipment Received By Rider for Shipper', 'type_id' => 1, 'subject' => 'Shipment Received by Rider', 'body' => 'Dear [company_name],' . PHP_EOL . 'Following of your shipment(s) have been picked by TRAX Rider on [arrival_at]:' . PHP_EOL . '[tracking_number] [order_id] [consignee_name] [consignee_city] [weight] [amount]' . PHP_EOL . 'Please contact at info@trax.pk or 021-111-11-8729 for further details.' . PHP_EOL . 'Regards,' . PHP_EOL . 'Team Trax', 'updated_by' => 7, 'status' => 0),
        ));
    }
}
