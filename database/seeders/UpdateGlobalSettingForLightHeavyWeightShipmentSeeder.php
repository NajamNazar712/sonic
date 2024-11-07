<?php

use Illuminate\Database\Seeder;

class UpdateGlobalSettingForLightHeavyWeightShipmentSeeder extends Seeder
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
            array('type' => 'light_heavy_weight_for_shipment', 'setting_value' => 0, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
