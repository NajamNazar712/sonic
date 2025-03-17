<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionForEmailDeliveryTimeSettingScreen extends Seeder
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
            ['id' => 1031, 'name' => 'Email Delivery Time', 'module_id' => 14],
        ]);

        // if new screen or excel
        DB::table('activity_trail_actions')->insert([
            ['id' => 824, 'screen_name' => 'Email Delivery Time', 'action' => 'View', 'created_at' => $timestamp, 'updated_at' => $timestamp]
        ]);

        // if new screen
        DB::table('admins_screen_list')->insert([
            [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Setting > Reports > Email Delivery Time',
                'url' => 'admin.settings.email_delivery_time.index',
                'permission_id' => 1031
            ],
        ]);
    }
}
