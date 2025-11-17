<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class PermissionForSubHubScreen extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 835, 'screen_name' => 'SUB Station visibility', 'action'=> 'View'),
            array('id' => 836, 'screen_name' => 'SUB Station visibility', 'action'=> 'Excel Download'),
        ));

        DB::table('module_permissions')->insert(array(
            array('id' => 1051, 'name' => 'SUB Station visibility', 'module_id' => 9),
        ));

        DB::table('admins_screen_list')->insert(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > SUB Station visibility', 'url'=>'admin.reports.sub_hub.index', 'permission_id' => 1051)
        );
    }
}
