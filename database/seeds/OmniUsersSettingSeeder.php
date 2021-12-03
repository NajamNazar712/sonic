<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class OmniUsersSettingSeeder extends Seeder
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
            array('type' => 'omni_users', 'setting_value' => 0, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
