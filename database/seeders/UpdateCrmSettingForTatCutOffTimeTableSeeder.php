<?php

use Illuminate\Database\Seeder;

class UpdateCrmSettingForTatCutOffTimeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('crm_settings')->insert(array(
            array('name' => 'TAT Cut-Off Time From', 'setting_value' => '9:00 AM', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('name' => 'TAT Cut-Off Time To', 'setting_value' => '5:00 PM', 'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));
    }
}
