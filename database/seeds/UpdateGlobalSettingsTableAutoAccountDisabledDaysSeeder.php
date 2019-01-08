<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
class UpdateGlobalSettingsTableAutoAccountDisabledDaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('global_settings')->insert(array(
            array('type' => 'auto_account_disabled_days', 'setting_value' => 30, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
