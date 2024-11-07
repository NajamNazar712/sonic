<?php

use Illuminate\Database\Seeder;

class UpdateGlobalSettingForAutoCrmCommentTableSeeder extends Seeder
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
            array('type' => 'auto_crm_comment', 'setting_value' => 1, 'text' => "Dear Shipper, please be informed that we have received your submitted complaint, and we are working on it. We will update you soon.", 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
