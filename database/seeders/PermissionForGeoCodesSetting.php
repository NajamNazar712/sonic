<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionForGeoCodesSetting extends Seeder
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
            ['id' => 1040, 'name' => 'Viw Geocode Settings', 'module_id' => 14],
            ['id' => 1041, 'name' => 'Generate Geocode', 'module_id' => 14],
        ]);

        // if new screen or excel
        DB::table('activity_trail_actions')->insert([
            ['id' => 832, 'screen_name' => 'Viw Geocode Settings', 'action' => 'Excel'],
        ]);

        // if new screen
        DB::table('admins_screen_list')->insert([
            [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Setting > Support > Geocodes Settings',
                'url' => 'admin.settings.geo_codes.index',
                'permission_id' => 1040
            ],
        ]);

    }
}
