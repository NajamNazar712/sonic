<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CreateNotificationForShipperLogisticBookingTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now();

        $timestamp = now(); // Or any valid timestamp

        DB::table('notifications')->insert(array(
            array(
                'id' => 232,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Shipper Logistic Bookings',
                'type_id' => 1,
                'subject' => 'Trax Logistic Booking Information',
                'body' => 'Dear Valued Customer,' . PHP_EOL . PHP_EOL .'Thank you for using Trax Services,' . PHP_EOL . PHP_EOL .'This is to inform you that the following shipment(s) were picked up by Trax on [Booking_at]. Kindly review the information, and if any discrepancies are observed, please revert within 48 hours of receiving this email notification.' . PHP_EOL . PHP_EOL.'[preview]'. PHP_EOL . PHP_EOL. 'Regards,' . PHP_EOL . 'Team Trax', 'updated_by' => 3495,'status' => 0
            )
        ));
    }
}
