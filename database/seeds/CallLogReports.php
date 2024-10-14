<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CallLogReports extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        DB::table('module_permissions')->insert(array(
            array('id' => 1011, 'name' => 'Bot Call Logs - View', 'module_id' => 14),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 810, 'screen_name' => 'Bot Call Logs', 'action' => 'View'),
        ));


        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile > Reason Validation > Bot Call Logs', 'url' => 'admin.reports.bot_rvr_log.index', 'permission_id' => 1011),
        ));
    }
}
