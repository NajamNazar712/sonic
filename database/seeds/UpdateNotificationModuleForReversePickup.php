<?php

use Illuminate\Database\Seeder;

class UpdateNotificationModuleForReversePickup extends Seeder
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
            array('id' => 77, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reverse Pickup Receiving', 'type_id' => 2, 'subject' => null, 'body' => 'Dear Shipper,' . PHP_EOL . PHP_EOL .'Rider [rider_name] is on the way for pickup from your address. We have sent address label to your email address, please paste it on packed shipment.', 'updated_by' => 6, 'status' => 0)
        ));
    }
}
