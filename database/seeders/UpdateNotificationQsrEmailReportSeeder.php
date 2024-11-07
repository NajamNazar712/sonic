<?php

use Illuminate\Database\Seeder;

class UpdateNotificationQsrEmailReportSeeder extends Seeder
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
            array('id' => 226, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Receive Quality of Service Report Email', 'type_id' => 1, 'subject' => 'Quality of Service Report', 'body' => 'Please find below the link to download Quality of Service Report.' . PHP_EOL . '[link]', 'updated_by' => 7, 'status' => 1)
        ));
    }
}
