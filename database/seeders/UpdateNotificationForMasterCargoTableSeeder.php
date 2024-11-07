<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateNotificationForMasterCargoTableSeeder extends Seeder
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
            array('id' => 86, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Short Received Shipment Hub Report Email', 'type_id' => 1, 'subject' => 'Short Received Shipment [hub] of [date]', 'body' => 'Please check short received shipment report dated [date]' . PHP_EOL . PHP_EOL . PHP_EOL .'[link]', 'updated_by' => 7, 'status' => 0),
            array('id' => 87, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Master Cargo Slip', 'type_id' => 1, 'subject' => 'Master Cargo Slip [hub] of [date]', 'body' => 'Please download Master Cargo slip from the following link: [link].', 'updated_by' => 7, 'status' => 0)
        ));
    }
}
