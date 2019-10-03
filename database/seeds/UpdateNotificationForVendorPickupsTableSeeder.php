<?php

use Illuminate\Database\Seeder;

class UpdateNotificationForVendorPickupsTableSeeder extends Seeder
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
            array('id' => 43, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Vendor Pickups', 'type_id' => 1, 'subject' => '[shipper_name] Booked Shipments', 'body' => 'Dear [vendor], ' . PHP_EOL . '[shipper_name] booked the following shipments to be picked from your address today.' . PHP_EOL . 'Please get your shipments ready.' . PHP_EOL . PHP_EOL . '[shipments_detail]' . PHP_EOL . PHP_EOL . 'Regards,'. PHP_EOL . 'Team Trax', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
