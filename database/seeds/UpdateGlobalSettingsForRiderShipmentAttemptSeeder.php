<?php

use Illuminate\Database\Seeder;

class UpdateGlobalSettingsForRiderShipmentAttemptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        DB::table('global_settings')->insert(array(
            array('type' => 'rider_shipment_attempt_count', 'setting_value' => 3, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('type' => 'rider_shipment_attempt_waiting_duration', 'setting_value' => 15, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
