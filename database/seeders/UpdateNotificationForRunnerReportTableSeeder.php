<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateNotificationForRunnerReportTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        DB::table('notifications')->insert(array(
            array('id' => 88, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Runner Report', 'type_id' => 1, 'subject' => 'Runner Report for [runner] [date]', 'body' => 'Please download the report from the following link: [link].', 'updated_by' => 7, 'status' => 0)
        ));
    }
}
