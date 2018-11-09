<?php

use Illuminate\Database\Seeder;

use Carbon\Carbon;

class UpdateGlobalSettingsTableShipmentCancellationCutOffDaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('global_settings')->insert(array(
            array('type' => 'shipment_cancellation_cut_off_days', 'setting_value' => 3, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
