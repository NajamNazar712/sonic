<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReattemptRvReport extends Seeder
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
            array('id' => 1008, 'name' => 'Reattempt Analysis Report', 'module_id' => 9),
        ));


        DB::table('activity_trail_actions')->insert(array(
            array('id' => 806, 'screen_name' => 'Reattempt Analysis Report', 'action' => 'View'),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 807, 'screen_name' => 'Reattempt Analysis Report', 'action' => 'Excel Download'),
        ));


        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Reattempt Analysis Report', 'url' => 'admin.reports.rvr_reattempt.index', 'permission_id' => 1008),
        ));
    }
}
