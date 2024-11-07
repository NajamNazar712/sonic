<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdatePermissionsForParcelValueBypassSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 910, 'name' => 'Parcel Value Bypass Users Setting - View', 'module_id' => 14)
        ));

        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 707, 'screen_name' => 'Parcel Value Bypass User Setting', 'action'=> 'View'),
        ));

        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Shipper > Parcel Value Bypass User Setting', 'url'=>'admin.settings.parcel_value_bypass.index', 'permission_id' => 910),
        ));
    }
}
