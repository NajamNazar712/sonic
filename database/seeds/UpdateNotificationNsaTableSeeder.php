<?php

use Illuminate\Database\Seeder;

class UpdateNotificationNsaTableSeeder extends Seeder
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
            array('id' => 32, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Non Service Area', 'type_id' => 1, 'subject' => 'Non Service Area Detected!', 'body' => 'Dear Shipper, ' . PHP_EOL . 'Please note that a keyword(s) “[nsa]” is Detected! In the consignee address of shipment: [tracking_number].' . PHP_EOL . 'In case of,' . PHP_EOL . 'Out of Service Area: Additional charges may apply.' . PHP_EOL . 'Non Service Area: Shipment may be returned.' . PHP_EOL . 'Please review the consignee address before dispatching the shipment.' . PHP_EOL . PHP_EOL . PHP_EOL  . 'For further assistance, please get in touch with our customer support at 021-38772222.', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
