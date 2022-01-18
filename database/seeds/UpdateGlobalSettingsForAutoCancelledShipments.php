<?php

use Illuminate\Database\Seeder;

class UpdateGlobalSettingsForAutoCancelledShipments extends Seeder
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
            array('type' => 'cancelled_shipments', 'setting_value' => 60, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
