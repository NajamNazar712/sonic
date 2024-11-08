<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class botCallReport extends Seeder
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
            array('id' => 1010, 'name' => 'Bot Report', 'module_id' => 9),
        ));
        
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 809, 'screen_name' => 'Bot Report', 'action' => 'View'),
        ));

        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Bot Report', 'url' => 'admin.reports.bot_rvr.index', 'permission_id' => 1010),
        ));
    }
}
