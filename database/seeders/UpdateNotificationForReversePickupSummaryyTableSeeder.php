<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateNotificationForReversePickupSummaryyTableSeeder extends Seeder
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
            array('id' => 78, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reverse Pickup Summary Hub Wise', 'type_id' => 1, 'subject' => 'Reverse Pickup Summary [hub] [date]', 'body' => '[preview]', 'updated_by' => 7, 'status' => 0),
            array('id' => 79, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reverse Pickup Summary Zone Wise', 'type_id' => 1, 'subject' => 'Reverse Pickup Summary [zone] [date]', 'body' => '[preview]', 'updated_by' => 7, 'status' => 0),
            array('id' => 80, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reverse Pickup Summary Overall', 'type_id' => 1, 'subject' => 'Reverse Pickup Summary Overall [date]', 'body' => '[preview]', 'updated_by' => 7, 'status' => 0),
        ));
    }
}
