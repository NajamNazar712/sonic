<?php

use Illuminate\Database\Seeder;

class UpdateGlobalSettingsTableMaximumConsolidationShipmentsSeeder extends Seeder
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
            array('type' => 'maximum_consolidation_shipments', 'setting_value' => 10, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
