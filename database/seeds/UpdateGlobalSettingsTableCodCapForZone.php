<?php

use Illuminate\Database\Seeder;

class UpdateGlobalSettingsTableCodCapForZone extends Seeder
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
            array('setting_value' => 1000000, 'type' => 'cod_cap_for_zone_class_0', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('setting_value' => 1000000, 'type' => 'cod_cap_for_zone_class_1', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('setting_value' => 1000000, 'type' => 'cod_cap_for_zone_class_2', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('setting_value' => 30000, 'type' => 'cod_cap_for_zone_class_3', 'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));
    }
}
