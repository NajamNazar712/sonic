<?php

use Illuminate\Database\Seeder;

class UpdateGlobalSettingsForTraxInternId extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now();

        DB::table('global_settings')->insert(array(
            array('type' => 'latest_intern_id', 'setting_value' => 250, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
