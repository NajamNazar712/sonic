<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateNotificationsForDailyFakeStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('notifications')->insert(array(
            array('id' => 53, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Daily Fake Status Report Station Wise', 'type_id' => 1, 'subject' => 'Daily Fake Status Report [hub] [date]', 'body' => '[preview]' . PHP_EOL . PHP_EOL .'Please download the report from the following link: [link].', 'updated_by' => 3, 'status' => 0),
            array('id' => 54, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Daily Fake Status Report Zone Wise', 'type_id' => 1, 'subject' => 'Daily Fake Status Report [zone] [date]', 'body' => '[preview]' . PHP_EOL . PHP_EOL .'Please download the report from the following link: [link].', 'updated_by' => 3, 'status' => 0),
            array('id' => 55, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Daily Fake Status Report Overall', 'type_id' => 1, 'subject' => 'Daily Fake Status Report Overall [date]', 'body' => '[preview]' . PHP_EOL . PHP_EOL .'Please download the report from the following link: [link].', 'updated_by' => 3, 'status' => 0),
        ));
    }
}
