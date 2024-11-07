<?php

use Illuminate\Database\Seeder;

class Seeder4531ForEmailNotification extends Seeder
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
            array('id' => 168, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Telenor Daily Shipment Status', 'type_id' => 1, 'subject' => 'Shipment Status Report - [date]', 'body' => 'Please check shipment status report dated [date]' . PHP_EOL . PHP_EOL . PHP_EOL .'[preview]', 'updated_by' => 7, 'status' => 0),
        ));
    }
}
