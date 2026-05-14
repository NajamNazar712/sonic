<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use DB;

class PermissionSeederForTPaymentExcludeShippers extends Seeder
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
            ['id' => 1072, 'name' => 'T Payment Exclude Shippers - View', 'module_id' => 14],
        ]);

        // if new screen or excel
        DB::table('activity_trail_actions')->insert([
            ['id' => 847 , 'screen_name' => 'T Payment Exclude Shippers', 'action' => 'View'],
            ['id' => 848, 'screen_name' => 'T Payment Exclude Shippers', 'action' => 'Submit'],
        ]);

        // if new screen
        DB::table('admins_screen_list')->insert([
            [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Setting > Shippers > T Payment Exclude Shippers',
                'url' => 'admin.settings.t_payment_exclude_shippers.index',
                'permission_id' => 1072
            ],
        ]);
    }
}

