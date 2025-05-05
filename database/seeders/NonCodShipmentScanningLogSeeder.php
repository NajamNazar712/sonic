<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NonCodShipmentScanningLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        // Delete entries based on permission_id only
        DB::table('admins_screen_list')
            ->where('permission_id', 1033)
            ->delete();

        DB::table('module_permissions')
            ->where('id', 1033)
            ->delete();

        // Insert new permission
        DB::table('module_permissions')->insert([
            ['id' => 1033, 'name' => 'Non-COD Shipments Scanning History', 'module_id' => 6],
        ]);

        // Insert new screen
        DB::table('admins_screen_list')->insert([
            [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Last Mile > Delivery > Non-COD Shipments Scanning History',
                'url' => 'admin.shipment_otp.scanning_history',
                'permission_id' => 1033
            ],
        ]);
    }

}
