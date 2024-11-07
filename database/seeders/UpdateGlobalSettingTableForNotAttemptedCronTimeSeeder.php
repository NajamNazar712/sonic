<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateGlobalSettingTableForNotAttemptedCronTimeSeeder extends Seeder
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
            array('type' => 'not_attempted_cron_time', 'setting_value' => 13, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
