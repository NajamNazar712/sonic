<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateGlobalSettingsTableDailyPickupSalesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now();

        DB::table('global_settings')->insert(array(
            array('type' => 'daily_pickup_sales_cron_time', 'setting_value' => 8, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
