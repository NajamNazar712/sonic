<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForCompletedAgingAndPendingCashCollectionTableSeeder extends Seeder
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
            array('id' => 71, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Completed Aging Report', 'type_id' => 1, 'subject' => 'Completed Aging Report [date]', 'body' => 'Trax Online Private Limited'.PHP_EOL.'Recovery Control Sheet'.PHP_EOL.PHP_EOL.'[preview]', 'updated_by' => 7, 'status' => 0),
            array('id' => 72, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Pending Cash Collection Report', 'type_id' => 1, 'subject' => 'Pending Cash Collection Aging Report [date]', 'body' => 'Trax Online Private Limited'.PHP_EOL.'Recovery Control Sheet'.PHP_EOL.PHP_EOL.'[preview]', 'updated_by' => 7, 'status' => 0)
        ));
    }
}
