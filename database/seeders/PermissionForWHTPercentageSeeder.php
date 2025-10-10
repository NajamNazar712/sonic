<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use DB;


class PermissionForWHTPercentageSeeder extends Seeder
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
            ['id' => 1037, 'name' => 'Product Tax Percentage', 'module_id' => 14],
        ]);

        // if new screen or excel
        DB::table('activity_trail_actions')->insert([
            ['id' => 831, 'screen_name' => 'Product Tax Percentage', 'action' => 'Update'],
        ]);

        // if new screen
        DB::table('admins_screen_list')->insert([
            [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Setting > Financials > Product Tax Percentage',
                'url' => 'admin.settings.product_tax.index',
                'permission_id' => 1037
            ],
        ]);

    }
}
