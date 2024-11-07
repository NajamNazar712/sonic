<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateNotificationsForOvernightOverlandCargoEmailTableSeeder extends Seeder
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
            array('id' => 59, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Overnight/Overland Cargo Report', 'type_id' => 1, 'subject' => '[shipping_mode] Cargo Report [date]', 'body' => '[preview]' . PHP_EOL . PHP_EOL .'Please download the report from the following link: [link].', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
