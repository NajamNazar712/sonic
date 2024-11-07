<?php

use Illuminate\Database\Seeder;

class AirwayBillAddressVisibilityModulePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 887, 'name' => 'Airway Bill Address Visibility - View', 'module_id' => 14),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 680, 'screen_name' => 'Airway Bill Address Visibility', 'action'=> 'View'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Bookings > Airway Bill Address Visibility', 'url'=>'admin.settings.airway_bill_address_visibility.index', 'permission_id' => 887),
        ));

        DB::table('global_settings')->insert(array(
            array('setting_value' =>  0 , 'type' => "airway_bill_address_visibility_setting", 'created_at'=> \Carbon\Carbon::now()),
        ));

    }
}
