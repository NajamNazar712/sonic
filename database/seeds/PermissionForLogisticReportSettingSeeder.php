<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PermissionForLogisticReportSettingSeeder extends Seeder
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
            ['id' => 932, 'name' => 'Logistic Setting - View', 'module_id' => 14],
        ]);

        // if new screen or excel
        DB::table('activity_trail_actions')->insert([
            ['id' => 735, 'screen_name' => 'Logistic Setting', 'action' => 'View'],
            ['id' => 736, 'screen_name' => 'Logistic Setting', 'action' => 'Submit'],
        ]);

        // if new screen
        DB::table('admins_screen_list')->insert([
            [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Setting > Shippers > Logistic Report Setting',
                'url' => 'admin.settings.logistic_report.index',
                'permission_id' => 932
            ],
        ]);
    }
}
