<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateNotificationsForSalesReportsRevampTableSeeder extends Seeder
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
            array('id' => 120, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Daily Pickup & Sales Report for Sales Person', 'type_id' => 1, 'subject' => 'Daily Pickup & Sales Report [date]', 'body' => 'Dear Concern,' . PHP_EOL . 'Please download the report from the following link: [link].', 'updated_by' => 6, 'status' => 0),
            array('id' => 121, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Daily Pickup & Sales Report for Regional Manager', 'type_id' => 1, 'subject' => 'Daily Pickup & Sales Report [date]', 'body' => 'Dear Concern,' . PHP_EOL . 'Please download the report from the following link: [link].', 'updated_by' => 6, 'status' => 0)
        ));
    }
}
