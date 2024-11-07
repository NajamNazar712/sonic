<?php

use Illuminate\Database\Seeder;

class UpdateAppNotificationForPaymentRecieveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('app_notifications')->insert(array(
            array('id' => 19, 'name' => 'Consignee Payment - Received', 'title' => 'Consignee Payment Received', 'body' => 'Dear [rider],' . PHP_EOL. 'Consignee has paid the COD Amount RS: [amount] of this shipment [tracking_number].', 'app_id' => 1,'updated_by' => 664,'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));

        DB::table('notifications')->insert(array(
            array('id' => 185, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Consignee Payment - Received', 'type_id' => 2, 'subject' => null, 'body' => 'Dear [rider]' . PHP_EOL . 'Consignee has paid the COD Amount RS: [amount] of this shipment [tracking_number].', 'updated_by' => 664, 'status' => 1)
        ));
    }
}
