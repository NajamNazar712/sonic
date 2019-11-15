<?php

use Illuminate\Database\Seeder;

class UpdateNotificationsForConsolidatedReturnDeliveredToShipperEmail extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('notifications')->where('id', 39)->delete();
        DB::table('notifications')->insert(array(
            array('id' => 39, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Return Delivered To Shipper', 'type_id' => 1, 'subject' => 'Shipment Return Delivered to Shipper', 'body' => 'Dear [company_name],' . PHP_EOL .'Please check these shipment(s) that are returned to your address as follows:'. PHP_EOL . PHP_EOL . PHP_EOL . '[tracking_number]' . '[status_updated_at]' . '[receiver_name]', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
