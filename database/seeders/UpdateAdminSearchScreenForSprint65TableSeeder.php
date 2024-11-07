<?php

use Illuminate\Database\Seeder;

class UpdateAdminSearchScreenForSprint65TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings  >  Support  >  App Slider', 'url'=>'admin.settings.rider_ticker.index', 'permission_id' => 466));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Human Resource > Employee Attendance', 'url'=>'admin.attendance.index', 'permission_id' => 465));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings >Financials > Petty Cash > Hub Assigning', 'url'=>'admin.settings.petty_cash.consignee.index', 'permission_id' => 462));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile >Delivery > Quick Receiving', 'url'=>'admin.delivery.quick_receiving.index', 'permission_id' => 464));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Human Resource > Employee Directory', 'url'=>'admin.human_resource.employee_directory.index', 'permission_id' => 467));
    }
}
