<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateActivityTrailAndPermissionForGeoCodesSettings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 1056, 'name' => 'Manage Geo Code Settings (TPL)', 'module_id' => 14 ),
            array('id' => 1057, 'name' => 'Update Geo Code Settings (TPL)', 'module_id' => 14 ),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 837, 'screen_name' => 'Geocodes Settings (TPL)', 'action' => 'View'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert([
            [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Setting > Support > Manage Geo Code Settings (TPL)',
                'url' => 'admin.settings.geo_codes.global_setting.index',
                'permission_id' => 1056
            ],
        ]);
    }
}
