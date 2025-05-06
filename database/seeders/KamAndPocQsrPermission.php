<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class KamAndPocQsrPermission extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Delete existing entries by ID (if they exist)
        DB::table('module_permissions')->where('id', 1032)->delete();
        DB::table('activity_trail_actions')->whereIn('id', [825, 826])->delete();
        DB::table('admins_screen_list')->where('permission_id', 1032)->delete();

        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        DB::table('module_permissions')->insert([
            ['id' => 1032, 'name' => 'KAM & POC QSR', 'module_id' => 9],
        ]);

        // if new screen or excel
        DB::table('activity_trail_actions')->insert([
            ['id' => 825, 'screen_name' => 'Reports > KAM & POC QSR', 'action' => 'View', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id' => 826, 'screen_name' => 'Reports > KAM & POC QSR', 'action' => 'Excel Download', 'created_at' => $timestamp, 'updated_at' => $timestamp],
        ]);

        // if new screen
        DB::table('admins_screen_list')->insert([
            [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Reports > KAM & POC QSR',
                'url' => 'admin.reports.kam_and_poc_qsr.index',
                'permission_id' => 1032
            ],
        ]);
    }
}
