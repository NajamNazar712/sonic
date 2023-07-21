<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForSalesTierTaggingHistory extends Seeder
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
            array('id' => 218, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Sales Tier Tagging History', 'type_id' => 1, 'subject' => 'Sales Tier Tagging History', 'body' => 'This is to inform you that you have been assigned as a new sales person for the following shipper(s).'. PHP_EOL .'[preview]' , 'updated_by' => 7, 'status' => 0)
        ));
    }
}
