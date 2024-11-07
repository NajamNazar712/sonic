<?php

use Illuminate\Database\Seeder;

class Seeder4922ForNotification extends Seeder
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
            array('id' => 175, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Weekly Sales Visit Report Individual', 'type_id' => 1, 'subject' => 'Weekly Sales Visit Report', 'body' => 'Dear Concern,
Please download the report from the following link: [link].', 'updated_by' => 3, 'status' => 0),
        ));
    }
}
