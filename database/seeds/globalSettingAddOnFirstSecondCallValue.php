<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class globalSettingAddOnFirstSecondCallValue extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('global_settings')->insert(array(
            array('type' => 'second_bot_call', 'setting_value' => 15, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('type' => 'third_bot_call', 'setting_value' => 60, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
