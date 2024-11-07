<?php

use Illuminate\Database\Seeder;

class UpdateAdminsScreenListTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings  >  Last Mile  >  Network Management  >  Riders  >  Riders Pending Request', 'url'=>'admin.management.riders.rider_request.index', 'permission_id' => 425),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings  >  Last Mile  >  Network Management  >  Walk-In City List ', 'url'=>'admin.management.city_list', 'permission_id' => 205),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports  >  Last Mile App Report', 'url'=>'admin.reports.last_mile_app.index', 'permission_id' => 437),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports  >  Month Closing Report-Individual', 'url'=>'admin.reports.month_closing.individual.index', 'permission_id' => 414),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile  >  Delivery  >  Delivery Consignee Signature', 'url'=>'admin.delivery.signature.index', 'permission_id' => 441),
        ));
    }
}
