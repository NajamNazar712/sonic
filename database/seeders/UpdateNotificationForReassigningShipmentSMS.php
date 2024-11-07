<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateNotificationForReassigningShipmentSMS extends Seeder
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
            array('id' => 107, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'SMS for reassigning of shipments to New Rider', 'type_id' => 2, 'body'=>'Dear [new_rider_name]'.PHP_EOL.'Pickup Request ID:[pickup_request_id] has been reassigned by the Coordinator [pickup_coordinator_name] to you from Rider [old_rider_name].', 'updated_by'=> 3, 'status'=> 0)
        ));
    }
}