<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityTrailSeederForSettingBypassWeight extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 909, 'name' => 'Bypassing of weight entry Setting', 'module_id' => 14),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 706, 'screen_name' => 'Bypassing of weight entry Setting', 'action'=> 'View'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Shippers > Bypassing of weight entry Setting', 'url'=>'admin.settings.shippers.bypass_weight.index', 'permission_id' => 909)
        );

    }
}
