<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateAdminSearchSonicSeederForFuelManagement extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Support > User Management > Fuel Management', 'url'=>'admin.user_management.fuel_management.index', 'permission_id' => 447));
    }
}
