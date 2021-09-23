<?php

use Illuminate\Database\Seeder;

class Sprint77QuickSearchAndActivityTrailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        DB::table('activity_trail_actions')->insert(array(
//            array('id' => 439, 'screen_name' => 'Carrefour Bulk Arrival', 'action'=> 'View'),
//            array('id' => 440, 'screen_name' => 'Rider Tracking', 'action'=> 'View'),
//            array('id' => 441, 'screen_name' => 'International Shipments Status', 'action'=> 'View'),
//            array('id' => 442, 'screen_name' => 'Opertaion Service Level Report', 'action'=> 'View'),
//            array('id' => 443, 'screen_name' => 'Opertaion Service Level Report', 'action'=> 'Excel Download'),
//            array('id' => 444, 'screen_name' => 'Carrefour Accounts', 'action'=> 'View'),
//            array('id' => 445, 'screen_name' => 'Debriefing Time Settings', 'action'=> 'View'),
//            array('id' => 446, 'screen_name' => 'Last Mile Status Cron Time', 'action'=> 'View'),
//            array('id' => 447, 'screen_name' => 'DHL International Shipment Sync Time', 'action'=> 'View'),
//            array('id' => 448, 'screen_name' => 'International User', 'action'=> 'View'),
//            array('id' => 449, 'screen_name' => 'Telenor Shipment Status ETA', 'action'=> 'View'),
//        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support > Carrefour > Carrefour Bulk Arrival', 'url'=>'admin.carrefour.arrival.index', 'permission_id' => 579),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support > Telenor > Bulk Return', 'url'=>'admin.telenor.return.bulk_return', 'permission_id' =>525 ),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'First Mile > Rider Tracking', 'url'=>'admin.v2_pickups.rider_tracking.index', 'permission_id' =>446 ),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support > International Shipment Status', 'url'=>'admin.international.shipment_status.index', 'permission_id' => 508),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Operation Service Level Report', 'url'=>'admin.reports.operation_service_level.index', 'permission_id' => 524),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Work Code Master Report', 'url'=>'admin.reports.work_code_master.index', 'permission_id' => 532),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Shippers > CCD Bookings', 'url'=>'admin.settings.ccd_booking.index', 'permission_id' => 558),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Shippers > Carrefour Accounts', 'url'=>'admin.settings.carrefour_account.index', 'permission_id' => 580),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Last Mile > Debriefing Time Settings', 'url'=>'admin.settings.debriefing_time_setting.index', 'permission_id' => 526),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Reports > Last Mile Status Cron Time', 'url'=>'admin.settings.last_mile_cron.index', 'permission_id' => 565),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > International > DHL Sync Time', 'url'=>'admin.settings.dhl_sync_time.index', 'permission_id' => 581),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > International > Automation User', 'url'=>'admin.settings.international_automation_user.index', 'permission_id' => 582),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Telenor > Telenor Shipment Status ETA', 'url'=>'admin.settings.shipment_status_eta.index', 'permission_id' => 534),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Human Resource > Rider Incentives', 'url'=>'adminhuman_resource.rider_incentive.index.', 'permission_id' => 492),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile > Delivery > DN ByPass Request', 'url'=>'admin.delivery.note.request_index', 'permission_id' => 531)
        );
    }
}
