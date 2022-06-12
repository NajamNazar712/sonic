<?php

use Illuminate\Database\Seeder;
use  Carbon\Carbon;

class Seeder5166ForSetting extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('global_settings')->insert(array(
            array('type' => 'rider_otp', 'setting_value' => 1, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('type' => 'admin_otp', 'setting_value' => 1, 'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));
    }
}
