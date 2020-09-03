<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForTaggingHistory extends Seeder
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
            array('id' => 81, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Tagging History', 'type_id' => 1, 'subject' => 'Tagging History', 'body' => 'This is to notify you that the following shipper has these salesperson '. PHP_EOL .'[preview]' , 'updated_by' => 3, 'status' => 0)
        ));
    }
}
