<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PermissionForMmsSettingScreenSeeder extends Seeder
{
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('module_permissions')->insert([
            ['id' => 868, 'name' => 'MMS Setting - View', 'module_id' => 14],
        ]);

        // if new screen or excel
        DB::table('activity_trail_actions')->insert([
            ['id' => 658, 'screen_name' => 'MMS Setting', 'action' => 'View'],
        ]);

        // if new screen
        DB::table('admins_screen_list')->insert([
            [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Setting > Shippers > MMS Setting',
                'url' => 'admin.settings.mms_report.index',
                'permission_id' => 868
            ],
        ]);

        DB::table('admin_role_module_permissions')->insert([
            ['role_id' => 115, 'permission_id' => 868],
        ]);
    }
}
