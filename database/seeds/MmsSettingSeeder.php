<?php

use Illuminate\Database\Seeder;

class MmsSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('global_settings')->insert([
            'setting_value' => 0,
            'type' => 'mms_setting',
            'text' => '',
        ]);
    }
}
