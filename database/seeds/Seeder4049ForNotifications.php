<?php

use Illuminate\Database\Seeder;

class Seeder4049ForNotifications extends Seeder
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
            array('id' => 157, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Daily Pickup & Sales Report for KAEs', 'type_id' => 1, 'subject' => ' Daily Pickup & Sales Report [date]', 'body' => 'Dear Concern,
Please download the report from the following link: [link].', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
