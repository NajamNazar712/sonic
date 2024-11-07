<?php

use Illuminate\Database\Seeder;

class UpdateNotificationPendingDeliveriesEmailReportSeeder extends Seeder
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
            array('id' => 227, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Receive Pending Deliveries Report Email', 'type_id' => 1, 'subject' => 'Pending Deliveries Report', 'body' => 'Please find below the link to download Pending Deliveries Report.' . PHP_EOL . '[link]', 'updated_by' => 7, 'status' => 1)
        ));
    }
}
