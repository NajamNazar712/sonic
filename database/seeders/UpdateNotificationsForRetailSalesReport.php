<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateNotificationsForRetailSalesReport extends Seeder
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
            array('id' => 206, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Retail Sales Monthly Report By Arrival Date', 'type_id' => 1, 'subject' => 'Retail Sales Monthly Report By Arrival Date | (For Month Of [month] [year])' , 'body' => 'Dear Concern,'. PHP_EOL .PHP_EOL.'Please download the report from the following link: [link].', 'updated_by' => 6, 'status' => 0),
            array('id' => 207, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Retail Sales Monthly Report By Delivery Date', 'type_id' => 1, 'subject' => 'Retail Sales Monthly Report By Delivery Date | (For Month Of [month] [year])' , 'body' => 'Dear Concern,'. PHP_EOL .PHP_EOL.'Please download the report from the following link: [link].', 'updated_by' => 6, 'status' => 0)
        ));
    }
}
