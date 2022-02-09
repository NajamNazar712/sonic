<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateGlobalSettingsSeederForRCPSms extends Seeder
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
            array('type' => 'return_confirmation_pending_sms', 'setting_value' => 1,'text' => 3, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
