<?php

use Illuminate\Database\Seeder;

class BoltPickupReceivingShipmentTableSeeder extends Seeder
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
            array('id' => 73, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Shipment Rider Picked', 'type_id' => 1, 'subject' => 'Shipment(s) Picked by Rider', 'body' => 'Dear [company_name],' . PHP_EOL . 'Following of your shipments have been picked by TRAX Rider:' . PHP_EOL . '[tracking_numbers]' . PHP_EOL . PHP_EOL . PHP_EOL . 'Please contact at info@trax.pk or 0213-877-22-22 for further details.', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
