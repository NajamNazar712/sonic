<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use DB;

class PermissionForWHTReportSeeder extends Seeder
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
            ['id' => 1036, 'name' => 'With Holding Tax Report', 'module_id' => 9],
        ]);

        // if new screen or excel
        DB::table('activity_trail_actions')->insert([
            ['id' => 829, 'screen_name' => 'With Holding Tax Report', 'action' => 'View'],
            ['id' => 830, 'screen_name' => 'With Holding Tax Report', 'action' => 'Excel'],
        ]);

        // if new screen
        DB::table('admins_screen_list')->insert([
            [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Reports > With Holding Tax Report',
                'url' => 'admin.reports.wht.index',
                'permission_id' => 1036
            ],
        ]);
    }
}
