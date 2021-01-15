<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateNotificationsForRetailBookingTableSeeder extends Seeder
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
            array('id' => 115, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Retail Booking', 'type_id' => 2, 'subject' => null, 'body' => 'Dear Shipper [shipper],' . PHP_EOL .'Your shipment has been booked against the tracking number [tracking_number]', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
