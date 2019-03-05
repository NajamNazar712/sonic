<?php

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
            array('type' => '', 'setting_value' => 4, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
