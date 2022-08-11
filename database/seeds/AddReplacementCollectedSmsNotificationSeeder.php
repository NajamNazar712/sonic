<?php

use Illuminate\Database\Seeder;

class AddReplacementCollectedSmsNotificationSeeder extends Seeder
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
            array('id' => 183, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Replacement Collected - Shipper', 'type_id' => 2, 'subject' => null, 'body' => 'Dear [shipper]' . PHP_EOL . 'Your replacement parcel has been collected from [consignee] by TRAX rider with CN # [tracking_number].', 'updated_by' => 664, 'status' => 1),

            array('id' => 184, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Replacement Collected - Consignee', 'type_id' => 2, 'subject' => null, 'body' => 'Dear [consignee]' . PHP_EOL . 'Your replacement parcel has been handed over to TRAX rider with following CN # [tracking_number].', 'updated_by' => 664, 'status' => 1),
        ));
    }
}
