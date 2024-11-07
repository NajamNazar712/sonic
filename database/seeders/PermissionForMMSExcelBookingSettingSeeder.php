<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PermissionForMMSExcelBookingSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('module_permissions')->insert([
            ['id' => 938, 'name' => 'MMS Excel Booking Shippers - View', 'module_id' => 14],
        ]);

        // if new screen or excel
        DB::table('activity_trail_actions')->insert([
            ['id' => 747, 'screen_name' => 'MMS Excel Booking Shippers', 'action' => 'View'],
            ['id' => 748, 'screen_name' => 'MMS Excel Booking Shippers', 'action' => 'Submit'],
        ]);

        // if new screen
        DB::table('admins_screen_list')->insert([
            [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Setting > Shippers > MMS Excel Booking Shippers',
                'url' => 'admin.settings.mms_excel_booking_setting.index',
                'permission_id' => 938
            ],
        ]);
    }
}
