<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
class UpdateNotificationForAccountDisabilityToShipperTableSeeder extends Seeder
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
            array('id' => 57, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Account Disabled  30 days inactivity for Shipper', 'type_id' => 1, 'subject' => 'Account [account_id],[shipper_name] Disabled after 30 days of inactivity', 'body' => 'Dear Concern,' . PHP_EOL . 'Your account [shipper_name] has been disabled after 30 days of inactivity. Please contact your sales person.'. PHP_EOL . 'TRAX Logistics'. PHP_EOL . '0213-877-22-22'. PHP_EOL .'info@trax.pk', 'updated_by' => 3, 'status' => 0),
            array('id' => 58, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Account Disabled  30 days inactivity for Shipper', 'type_id' => 2, 'subject' => NULL, 'body' => 'Dear Concern,' . PHP_EOL . 'Your account [shipper_name] has been disabled after 30 days of inactivity. Please contact your sales person.', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
