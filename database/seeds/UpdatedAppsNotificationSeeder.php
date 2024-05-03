<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdatedAppsNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('app_notifications')->insert(array(
            array('id' => 22, 'name' => 'Shipment Reattempt Reminder', 'title' => 'Shipment Reattempt Requestd', 'body' => 'This is a reattempt request for shipment ([shipment_id]), kindly proceed.', 'app_id' => 1,'updated_by' => 346,'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
