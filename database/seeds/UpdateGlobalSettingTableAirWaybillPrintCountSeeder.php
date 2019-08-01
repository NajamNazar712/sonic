<?php

use Illuminate\Database\Seeder;

class UpdateGlobalSettingTableAirWaybillPrintCountSeeder extends Seeder
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
            array('type' => 'air_waybill_printing_count', 'setting_value' => 1, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
