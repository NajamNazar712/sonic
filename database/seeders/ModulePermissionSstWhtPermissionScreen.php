<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModulePermissionSstWhtPermissionScreen extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 1057, 'name' => 'Remove WHT & SST - View', 'module_id' => 8),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 838, 'screen_name' => 'Remove WHT & SST', 'action'=> 'View'),
            array('id' => 839, 'screen_name' => 'Remove WHT & SST', 'action'=> 'Excel Upload'),
        ));
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Finance > Remove WHT & SST', 'url'=>'admin.finance.removal_sst_wht.index', 'permission_id' => 1057)
        );
    }
}
