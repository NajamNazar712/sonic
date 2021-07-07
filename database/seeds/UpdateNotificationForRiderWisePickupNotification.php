<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForRiderWisePickupNotification extends Seeder
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
            array('id' => 139, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Rider Wise Pickup Notification', 'type_id' => 1, 'subject' => 'Riders Pickups' , 'body' => '[preview]', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
