<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UpdateNotificationForSalePersonNumbersEmailTableSeeder extends Seeder
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
            array('id' => 47, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Daily Sale Person Numbers Report', 'type_id' => 1, 'subject' => 'Daily Sale Person Numbers [date]', 'body' => '[preview]' . PHP_EOL . PHP_EOL .'Please download the report from the following link: [link].', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
