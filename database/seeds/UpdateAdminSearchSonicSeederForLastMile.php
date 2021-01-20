<?php

use Illuminate\Database\Seeder;

class UpdateAdminSearchSonicSeederForLastMile extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile > Delivery > Pending Cash Collection > Pending Cash Collection Retail', 'url'=>'admin.delivery.cash_collection.retail.index', 'permission_id' => 423));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile > Delivery > Pending Cash Collection > Pending Cash Collection COD', 'url'=>'admin.delivery.cash_collection.pending.index', 'permission_id' => 424));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile > Delivery > Completed > Completed COD', 'url'=>'admin.delivery.completed.index', 'permission_id' => 379));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile > Delivery > Completed > Completed Deliveries Retail', 'url'=>'admin.delivery.completed.retail.index', 'permission_id' => 40));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Confirmation Pending Shipments Report', 'url'=>'admin.reports.confirmation_pending_report.index', 'permission_id' => 430));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Month Closing Report-Individual', 'url'=>'admin.reports.month_closing.individual.index', 'permission_id' => 408));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Retail > Store Management > Franchise', 'url'=>'admin.retail.franchise.index', 'permission_id' => 380));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Retail > Store Management > Trax Center', 'url'=>'admin.retail.trax_center.index', 'permission_id' => 380));
    }
}
