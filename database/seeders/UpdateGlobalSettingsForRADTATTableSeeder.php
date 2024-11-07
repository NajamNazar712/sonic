<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateGlobalSettingsForRADTATTableSeeder extends Seeder
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
            array('type' => 'rad_tat_overnight', 'setting_value' => 3, 'text' => "", 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('type' => 'rad_tat_overland', 'setting_value' => 5, 'text' => "", 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
