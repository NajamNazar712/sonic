<?php

use Illuminate\Database\Seeder;

class CreateWeightBypassGlobalSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('global_settings')->where('type','bypass_weight_setting')->delete();
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('global_settings')->insert(array(
            array('type' => 'bypass_weight_setting', 'setting_value' => 1, 'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));
    }
}
