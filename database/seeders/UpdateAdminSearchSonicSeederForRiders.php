<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateAdminSearchSonicSeederForRiders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings  >  Last Mile  >  Network Management > Riders > Permanent', 'url'=>'admin.management.riders.permanent.index', 'permission_id' => 377));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings  >  Last Mile  >  Network Management > Riders > Incentive', 'url'=>'admin.management.riders.incentive.index', 'permission_id' => 378));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings  >  Last Mile  >  Network Management > Riders > Blacklisted', 'url'=>'admin.management.riders.blacklist.index', 'permission_id' => 379));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings  >  Last Mile  >  Network Management > Riders > SMS History', 'url'=>'admin.management.riders.sms_history.index', 'permission_id' => 380));
    }
}
