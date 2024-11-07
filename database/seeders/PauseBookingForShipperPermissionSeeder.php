<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PauseBookingForShipperPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        
        DB::table('module_permissions')->insert(array(
            array('id' => 999, 'name' => 'Active - Pause', 'module_id' => 2),
        ));
    }
}