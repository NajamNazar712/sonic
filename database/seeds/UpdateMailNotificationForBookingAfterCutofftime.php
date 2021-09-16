<?php

use Illuminate\Database\Seeder;

class UpdateMailNotificationForBookingAfterCutofftime extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('notifications')->where('id', 153)->delete();
        DB::table('notifications')->insert(array(
            array('id' => 153, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Booking After Cut Off Time', 'type_id' => 1, 'subject' => 'Shipment Booked after Cut Off Time' , 'body' => 'Dear [shipper_name], You have booked shipment after cut off time so we cannot commit pick up today but will try to arrange it or it will be done tomorrow. Thanks and Regards, Team TRAX', 'updated_by' => 3, 'status' => 1)
        ));
    }
}
