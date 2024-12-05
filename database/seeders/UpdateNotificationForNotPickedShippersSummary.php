<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateNotificationForNotPickedShippersSummary extends Seeder
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
            array('id' => 99, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Not Picked Shippers Summary', 'type_id' => 1, 'subject' => 'Not Picked Shippers Summary', 'body' => 'Dear [sales_person]'.PHP_EOL.'It is to notify you that shipments from below listed shippers were not picked.' . PHP_EOL .'[preview]','updated_by' => 3, 'status' => 0)
        ));
    }
}
