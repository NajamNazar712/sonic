<?php

use Illuminate\Database\Seeder;

class AddAppNotificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('app_notifications')->insert(array(
            array('id' => 1, 'name' => 'Pickup Request Reassigned-1', 'title' => 'Pickup Request Reassigned', 'body' => 'Dear Rider Pickup of [shipper_name] Has Been Reassigned To You From [rider]', 'app_id' => 1,'updated_by' => 664,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 2, 'name' => 'Pickup Request Reassigned-2', 'title' => 'Pickup Request Reassigned', 'body' => 'Dear Rider Pickup of [shipper_name] Has Been Reassigned To [rider]', 'app_id' => 1, 'updated_by' => 664,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 3, 'name' => 'Pickup Request Assigned', 'title' => 'Pickup Request Assigned', 'body' => 'Dear Rider Pickup of [shipper_name] Has Been Assigned To You', 'app_id' => 1, 'updated_by' => 664,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 4, 'name' => 'Pickup Request Auto Assigned', 'title' => 'Pickup Request Assigned', 'body' => 'Dear Rider Pickup of [shipper_name] Has Been Auto Assigned To You', 'app_id' => 1, 'updated_by' => 664,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 5, 'name' => 'Delivery Note Assigned', 'title' => 'Delivery Note Assigned', 'body' => 'Dear Rider Delivery Note # [note_id] Has Been Assigned To You', 'app_id' => 1, 'updated_by' => 664,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 6, 'name' => 'Return Note Assigned', 'title' => 'Return Note Assigned', 'body' => 'Dear Rider Return Note # [note_id] Has Been Assigned To You', 'app_id' => 1, 'updated_by' => 664,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 7, 'name' => 'Shipper Shipment Update', 'title' => 'Shipment Update', 'body' => 'Dear [shipper_name]' . PHP_EOL . 'Your Shipment [tracking_no] is moved to [status_name] status', 'app_id' => 2, 'updated_by' => 664,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 8, 'name' => 'Consignee Shipment Update', 'title' => 'Shipment Update', 'body' => 'Dear [consignee_name]' . PHP_EOL . 'Your Shipment [tracking_no] is moved to [status_name] status', 'app_id' => 2, 'updated_by' => 664,'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 9, 'name' => 'Delivery Note Creation OTP', 'title' => 'Delivery Note Creation OTP', 'body' => 'Dear Rider [rider] Your Otp for Delivery Note Creation is [otp]', 'app_id' => 1, 'updated_by' => 664,'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));
    }
}
