<?php

use Illuminate\Database\Seeder;

class UpdateAdminSearchSonicSeederForTerritory extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings  >  Last Mile  >  Network Management > Territory > Add Territory', 'url'=>'admin.management.territory.index', 'permission_id' => 443));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings  >  Last Mile  >  Network Management > Territory > Add Area', 'url'=>'admin.management.area.index', 'permission_id' => 443));
    }
}
