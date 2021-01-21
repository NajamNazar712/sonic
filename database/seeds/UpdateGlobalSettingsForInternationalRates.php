<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateGlobalSettingsForInternationalRates extends Seeder
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
            array('type' => 'international_fuel_surcharge', 'setting_value' => 100, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('type' => 'international_exchange_rate', 'setting_value' => 100, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
