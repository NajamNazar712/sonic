<?php

use Illuminate\Database\Seeder;

class UpdateNotificationsForRetailDeliveryTableSeeder extends Seeder
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
            array('id' => 116, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Retail Delivery', 'type_id' => 1, 'subject' => 'Retail delivery', 'body' => 'Dear Shipper [shipper],' . PHP_EOL .'Your shipment against tracking number [tracking_number] has been delivered', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
