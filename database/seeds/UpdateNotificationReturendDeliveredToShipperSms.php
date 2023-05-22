<?php

use Illuminate\Database\Seeder;

class UpdateNotificationReturendDeliveredToShipperSms extends Seeder
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
            array('id' => 216, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Returned Delivered To Shipper Sms', 'type_id' => 2, 'subject' => null, 'body' => 'Dear Customer,' . PHP_EOL . 'Hope you are doing great please note that below mentioned Total Shipments [shipments_count] are returned back to you in safe and sound condition today under Return Note Number  [return_notes_id]. In case of any query regarding these shipments you may respond us back in 48 hours.', 'updated_by' => 7, 'status' => 0)
        ));
    }
}
