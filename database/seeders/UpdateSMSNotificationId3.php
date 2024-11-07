<?php

use Illuminate\Database\Seeder;

class UpdateSMSNotificationId3 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('notifications')->where('id', 3)->delete();
        DB::table('notifications')->insert(array(
            array('id' => 3, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Shipment Arrived at Origin Office for Consignee', 'type_id' => 2, 'subject' => NULL, 'body' => 'Dear [consignee_name],' . PHP_EOL . 'Your order from [company_name] is picked by TRAX under [tracking_number].' . PHP_EOL . PHP_EOL . PHP_EOL . 'Please call 0304-11-11-232 for further details.'.PHP_EOL.''.PHP_EOL.'https://sonic.pk/tracking?tracking_number=[tracking_number]', 'updated_by' => 3, 'status' => 1),
        ));
    }
}
