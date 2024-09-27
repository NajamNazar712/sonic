<?php

use Illuminate\Database\Seeder;

class BulkBookingTrackingSettingAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        \Illuminate\Support\Facades\DB::table('global_settings')->insert(array(
            array('type' => 'bulk_tracking_shippers', 'setting_value' => 1, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('type' => 'bulk_booking_shippers', 'setting_value' => 1, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));

        \Illuminate\Support\Facades\DB::table('module_permissions')->insert(array(
            array('id' => 1009, 'name' => 'Bulk Booking And Tracking - View', 'module_id' => 14)
        ));

        \Illuminate\Support\Facades\DB::table('activity_trail_actions')->insert(array(
            array('id' => 808, 'screen_name' => 'Bulk Booking And Tracking', 'action'=> 'view')
        ));
    }
}
