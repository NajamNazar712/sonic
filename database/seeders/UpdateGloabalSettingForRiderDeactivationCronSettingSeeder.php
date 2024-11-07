<?php

use Illuminate\Database\Seeder;

class UpdateGloabalSettingForRiderDeactivationCronSettingSeeder extends Seeder
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
            array('type' => 'rider_deactivation_cron_days', 'setting_value' => 3, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('type' => 'rider_deactivation_cron_status', 'setting_value' => 1, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
