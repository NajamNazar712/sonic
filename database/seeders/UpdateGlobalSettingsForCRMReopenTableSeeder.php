<?php

use Illuminate\Database\Seeder;

class UpdateGlobalSettingsForCRMReopenTableSeeder extends Seeder
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
            array('type' => 'crm_reopen_count', 'setting_value' => 1, 'text' => "off", 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
