<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForPickupAppNotification extends Seeder
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
            array('id' => 83, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Pickup App Notification', 'type_id' => 1, 'subject' => 'TRAX - Shipments Picked Date [shipment_picked_date],[rider_name] , [shipper_name] , [requested_date]' , 'body' => 'Dear Shipper [shipper_name] '. PHP_EOL .PHP_EOL.'Your pickup for the day has been done by [rider_name]. We have picked [number] number of shipments. We will share tracking ID\'s and shipment weight via email. Please do reconcile your shipments and revert back in case of any discrepancy.'. PHP_EOL .PHP_EOL.'Regards'. PHP_EOL .PHP_EOL.'Team Trax', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
