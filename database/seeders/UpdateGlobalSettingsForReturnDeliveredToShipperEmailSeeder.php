<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
class UpdateGlobalSettingsForReturnDeliveredToShipperEmailSeeder extends Seeder
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
            array('type' => 'return_delivered_to_shipper_cut_off_time', 'setting_value' => 12, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
