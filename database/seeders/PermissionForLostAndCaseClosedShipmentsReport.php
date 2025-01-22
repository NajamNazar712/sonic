<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PermissionForLostAndCaseClosedShipmentsReport extends Seeder
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
            ['id' => 1021, 'name' => 'Lost/Case Closed Summary Report', 'module_id' => 9],
        ]);

        // if new screen or excel
        DB::table('activity_trail_actions')->insert([
            ['id' => 814, 'screen_name' => 'Lost/Case Closed Summary Report', 'action' => 'View', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 815, 'screen_name' => 'Lost/Case Closed Summary Report', 'action' => 'Excel Download', 'created_at' => $timestamp, 'updated_at' => $timestamp],
        ]);

        // if new screen
        DB::table('admins_screen_list')->insert([
            [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Reports > Lost/Case Closed Summary Report',
                'url' => 'admin.reports.lost_and_case_closed_summary_report.index',
                'permission_id' => 1021
            ],
        ]);

    }
}
