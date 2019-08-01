<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForShipmentDeliveredZeroCod extends Seeder
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
            array('id' => 35, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Shipment Delivered Zero COD', 'type_id' => 2, 'subject' => null, 'body' => 'Dear [consignee_name]'.PHP_EOL.'Your shipment from [shipper_name] having tracking number [tracking_number] is Delivered to [receiver_name].', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
