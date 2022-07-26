<?php

use Illuminate\Database\Seeder;

class BookingDestinationMappingModulePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 772, 'name' => 'Booking Destination Keyword - View', 'module_id' => 14),
            array('id' => 773, 'name' => 'Booking Destination Keyword - Add', 'module_id' => 14),
            array('id' => 774, 'name' => 'Booking Destination Keyword - Edit/Disable', 'module_id' => 14)
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 565, 'screen_name' => 'Booking Destination Keyword', 'action'=> 'View')
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Last Mile > Booking Destination Keywords', 'url'=>'admin.settings.booking_destination_keyword.index', 'permission_id' => 772),
//            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Last Mile > Add Booking Destination Keywords', 'url'=>'admin.settings.booking_destination_keyword.add', 'permission_id' => 773),
        ));
    }
}
