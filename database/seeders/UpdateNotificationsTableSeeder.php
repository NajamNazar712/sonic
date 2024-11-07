<?php

use Illuminate\Database\Seeder;

use Carbon\Carbon;

class UpdateNotificationsTableSeeder extends Seeder
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
        	array('id' => 23, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Return Confirmation Pending for Shipper', 'type_id' => 1, 'subject' => 'Shipment(s) Pending for Confirmation', 'body' => 'Dear [company_name],' . PHP_EOL . 'Please check these confirmation pending shipment(s) and advise us for further action.' . PHP_EOL . 'Kindly reply by 6:00 PM otherwise these will be proceeded for return.' . PHP_EOL . PHP_EOL . '[tracking_number][consignee_name][consignee_phone_number_1][consignee_address][pickup_city][consignee_city][amount][status][status_reason][status_date][arrival_date]' . PHP_EOL . PHP_EOL . PHP_EOL . 'Please contact at info@trax.pk or 0304-11-11-232 for further details.', 'updated_by' => 3, 'status' => 0),
        	array('id' => 24, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Shipment(s) Return Confirmed for Employees', 'type_id' => 1, 'subject' => 'Shipment(s) Return Confirmed of [hub]', 'body' => 'Dear Concern,' . PHP_EOL . 'Please dispatch these shipments to their respective origins ASAP as these are confirmed for return.' . PHP_EOL . PHP_EOL . '[tracking_number][consignee_name][pickup_city][consignee_city][status][status_date]', 'updated_by' => 3, 'status' => 0),
        	array('id' => 25, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Shipment(s) Re-Attempt for Employees', 'type_id' => 1, 'subject' => 'Shipment(s) for Re-Attempt of [hub]', 'body' => 'Dear Concern,' . PHP_EOL . 'Please dispatch these shipments for delivery ASAP as these are marked Re-Attempt.' . PHP_EOL . PHP_EOL . '[tracking_number][consignee_name][pickup_city][consignee_city][status][status_date]', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
