<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
use Carbon\Carbon;

class PermissionForExpectedShipmentPenaltyScreen extends Seeder
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
            ['id' => 1056, 'name' => 'Expected Shipment Penalties', 'module_id' => 8],
            
        ]);

        DB::table('admins_screen_list')->insert([
            [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'name' => 'Finance > Recovery > Expected Shipment Penalties',
                'url' => 'admin.finance.expected_shipment_penalty.index',
                'permission_id' => 1056
            ],
        ]);
        
    }
}
