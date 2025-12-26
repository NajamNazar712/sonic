<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Carbon\Carbon;

class PermissionForZeroCodShhippersBookingAllow extends Seeder
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
            ['id' => 1054, 'name' => 'Zero Cod Shippers', 'module_id' => 14],
            ['id' => 1055, 'name' => 'Zero Cod Shipper Logs', 'module_id' => 14],
            
        ]);


        // if new screen
        DB::table('admins_screen_list')->insert([
            [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Setting > Shipper > Zero Cod Shippers',
                'url' => 'admin.settings.zero_cod_shippers.index',
                'permission_id' => 1054
            ],
            [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Setting > Shipper > Zero Cod Shippers Logs',
                'url' => 'admin.settings.zero_cod_shippers.logs',
                'permission_id' => 1055
            ]
        ]);
    }
}
