<?php

use Illuminate\Database\Seeder;

class GlobalSettingForCnPrintRightsSeeder extends Seeder
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
            'type' => 'cn_print_rights',
            'text' => '',
        ]);
    }
}
