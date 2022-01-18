<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateNotificationTableSeederForDwsArrivalEmail extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        DB::table('notifications')->insert(array(
            array('id' => 166, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Shipment Arrived at Origin from DWS', 'type_id' => 1, 'subject' => 'Arrived at Origin', 'body' => 'Dear [company_name],' . PHP_EOL . 'Following of your shipments have been received by TRAX on [arrival_at]:' . PHP_EOL . '[tracking_number][order_id][consignee_name][consignee_city]' . PHP_EOL . PHP_EOL . PHP_EOL . 'Please contact at info@trax.pk or 0304-11-11-232 for further details.', 'updated_by' => 3, 'status' => 0),
        ));

    }
}
