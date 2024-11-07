<?php

use Illuminate\Database\Seeder;

class Seeder4608TotalTimeSettings extends Seeder
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
            array('type' => 'debriefing_total_time_setting','text' => 9, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
