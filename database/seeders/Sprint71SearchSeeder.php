<?php

use Illuminate\Database\Seeder;

class Sprint71SearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Human Resource > ERF Dashboard', 'url'=>'admin.human_resource.erf.index', 'permission_id' => 506));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Bookings > FTL > FTL Requests', 'url'=>'admin.ftl.request.index', 'permission_id' => 511));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Bookings > FTL > Walk-In FTL Booking', 'url'=>'admin.shipment.book.ftl.walk_in', 'permission_id' => 516));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Financials > Invoices > FTL Invoices', 'url'=>'admin.finance.ftl_invoice.index', 'permission_id' => 509));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support > International Shipments Status', 'url'=>'admin.international.shipment_status.index', 'permission_id' => 508));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Shipper Insurance', 'url'=>'admin.reports.shipper_insurance.index', 'permission_id' => 502));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Supply Chain > Fleet Management', 'url'=>'admin.settings.fleet.index', 'permission_id' => 498));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Supply Chain > Route Management', 'url'=>'admin.settings.route_management.index', 'permission_id' => 499));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Supply Chain > Runner > Vehicle In Transit', 'url'=>'admin.runner.intransit', 'permission_id' => 501));
    }
}
