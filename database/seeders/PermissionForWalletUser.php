<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionForWalletUser extends Seeder
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
            ['id' => 1022, 'name' => 'Wallet Users - List', 'module_id' => 8],
            
        ]);

        // if new screen or excel
        DB::table('activity_trail_actions')->insert([
            ['id' => 816, 'screen_name' => 'Wallet Users', 'action' => 'View'],
            ['id' => 817, 'screen_name' => 'Wallet Users', 'action' => 'Excel Download'],
        ]);

        // if new screen
        DB::table('admins_screen_list')->insert([
            [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Finance > Wallet Users',
                'url' => 'admin.finance.wallet_users.index',
                'permission_id' => 1022
            ],
        ]);
    }
}
