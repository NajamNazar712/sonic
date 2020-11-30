<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForNotPickedShippersSummarySMS extends Seeder
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
            array('id' => 105, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'SMS to sales person', 'type_id' => 2, 'subject' => null, 'body' => 'Dear [sales_person],' . PHP_EOL . 'It is to inform that shipments from [shipper_name] were not picked due to [reason]' . PHP_EOL . 'Regards,' . PHP_EOL . 'Team Trax', 'updated_by' => 3, 'status' => 1)
        ));
    }
}
