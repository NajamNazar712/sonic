<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForTelenorSalesReport extends Seeder
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
            array('id' => 96, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Telenor Sales Report', 'type_id' => 1, 'subject' => 'Telenor Sales Report', 'body' => 'Dear Concern' . PHP_EOL .'Please find below the link to download Telenor Sales Report.'. PHP_EOL . '[link]', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
