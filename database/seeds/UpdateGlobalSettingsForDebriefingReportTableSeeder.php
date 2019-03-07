<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateGlobalSettingsForDebriefingReportTableSeeder extends Seeder
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
            array('type' => 'debriefing_report_cut_off_time_start', 'setting_value' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
