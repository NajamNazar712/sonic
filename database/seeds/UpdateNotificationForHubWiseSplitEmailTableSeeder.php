<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UpdateNotificationForHubWiseSplitEmailTableSeeder extends Seeder
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
            array('id' => 48, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Daily Hub Wise Split Report', 'type_id' => 1, 'subject' => 'Daily Hub Wise Split [date]', 'body' => '[preview]' . PHP_EOL . PHP_EOL .'Please download the report from the following link: [link].', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
