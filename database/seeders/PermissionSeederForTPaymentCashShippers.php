<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use DB;

class PermissionSeederForTPaymentCashShippers extends Seeder
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
            ['id' => 1073, 'name' => 'T Payment Cash Shippers - View', 'module_id' => 14],
        ]);

        // if new screen or excel
        DB::table('activity_trail_actions')->insert([
            ['id' => 849 , 'screen_name' => 'T Payment Cash Shippers', 'action' => 'View'],
            ['id' => 850, 'screen_name' => 'T Payment Cash Shippers', 'action' => 'Submit'],
        ]);

        // if new screen
        DB::table('admins_screen_list')->insert([
            [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Setting > Shippers > T Payment Cash Shippers',
                'url' => 'admin.settings.t_payment_cash_shippers.index',
                'permission_id' => 1073
            ],
        ]);
    }
}
