<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityTrailAndPermissionForBaseRateRevisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 990, 'name' => 'Base Rate Revision - View', 'module_id' => 14),
        ));
        
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 796, 'screen_name' => 'Base Rate Revision', 'action'=> 'View'),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 797, 'screen_name' => 'Base Rate Revision', 'action'=> 'Excel Download'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Shippers > Base Rate Revision', 'url'=>'admin.settings.shippers.base_rate_revisions.index', 'permission_id' => 990),           
        ));
    }
}
