<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForPendingPickupRemarks extends Seeder
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
            array('id' => 177, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Pending Pickup Remarks Email', 'type_id' => 1, 'subject' => 'Dear [sales_person]', 'body' => 'Your Shipper [shipper_name] have a reverse pickup request [pickup_request_no] that have some issues in it.'. PHP_EOL . 'Please check its remarks [remarks] and conclude this ASAP.'. PHP_EOL . 'Thanks', 'updated_by' => 3, 'status' => 1)
        ));
    }
}
