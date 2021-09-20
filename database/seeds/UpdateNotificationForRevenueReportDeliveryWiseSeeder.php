<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForRevenueReportDeliveryWiseSeeder extends Seeder
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
            array('id' => 156, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Revenue Monthly Report By Delivery Date', 'type_id' => 1, 'subject' => ' Revenue Monthly Report By Delivery Date | (For Month Of [month] [year])' , 'body' => 'Dear Concern,'. PHP_EOL .PHP_EOL.'Please download the report from the following link: [link].', 'updated_by' => 7, 'status' => 0)
        ));
    }
}
