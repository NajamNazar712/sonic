<?php

use Illuminate\Database\Seeder;

class UpdateNotificationsForLostShipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->toDateTimeString();

        DB::table('notifications')->insert(array(
            array('id' => 107, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Lost Shipment', 'type_id' => 1, 'body'=>'Dear [shipper]'.PHP_EOL.'Shipment with Tracking Number [tracking_number] has been marked as Lost', 'updated_by'=> 6, 'status'=> 0)
        ));
    }
}
