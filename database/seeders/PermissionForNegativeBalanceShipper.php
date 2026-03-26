<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use DB;

class PermissionForNegativeBalanceShipper extends Seeder
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
            ['id' => 1070, 'name' => 'Negative Balance Shipper Report', 'module_id' => 9],
            
        ]);

        // if new screen or excel
        DB::table('activity_trail_actions')->insert([
            ['id' => 845, 'screen_name' => 'Negative Balance Shipper Report', 'action' => 'View'],
            ['id' => 846, 'screen_name' => 'Negative Balance Shipper Report', 'action' => 'Excel'],
        ]);

        // if new screen
        DB::table('admins_screen_list')->insert([
            [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Report > Negative Balance Shipper',
                'url' => 'admin.reports.negative_balance.index',
                'permission_id' => 1070
            ],
        ]);
    }
}
