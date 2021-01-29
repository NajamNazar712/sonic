<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateNotificationForLeadReceivedAndSalePersonTagTableSeeder extends Seeder
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
            array('id' => 203, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Lead(s) Received', 'type_id' => 1, 'subject' => 'New Lead(s) Received [date]', 'body' => 'Please check these new received Lead(s)' . PHP_EOL . PHP_EOL . '[preview].', 'updated_by' => 7, 'status' => 0),
            array('id' => 204, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Sale Person Tagged On Lead(s)', 'type_id' => 1, 'subject' => 'New Lead(s) Tagged [date]', 'body' => 'Dear [sale_person],' . PHP_EOL . 'Following Lead(s) has been tagged to you' . PHP_EOL . PHP_EOL . '[preview].', 'updated_by' => 7, 'status' => 0)
        ));
    }
}
