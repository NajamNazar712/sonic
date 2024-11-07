<?php

use Illuminate\Database\Seeder;
class GlobalSettingAddCrmAgentStatus extends Seeder
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
            array('type' => 'crm_agent_auto_assigning', 'setting_value' => 0, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
