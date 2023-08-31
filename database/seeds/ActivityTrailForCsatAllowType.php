<?php

use Illuminate\Database\Seeder;

class ActivityTrailForCsatAllowType extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 696, 'screen_name' => 'CSAT Cases-Type Setting', 'action'=> 'View'),
            array('id' => 697, 'screen_name' => 'CSAT Cases-Type Setting', 'action'=> 'Excel Download'),
            array('id' => 701, 'screen_name' => 'CSAT Formula Setting', 'action'=> 'View'),
            array('id' => 702, 'screen_name' => 'CSAT Formula Setting', 'action'=> 'Excel Download'),
        ));
        DB::table('module_permissions')->insert(array(
            array('id' => 900, 'name' => 'CSAT Cases-Type Setting - View', 'module_id' => 14),
            array('id' => 902, 'name' => 'CSAT Formula Setting - View', 'module_id' => 14),

        ));
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > CRM > CSAT Cases-Type Setting', 'url'=>'admin.settings.csat_cases_setting.index', 'permission_id' => 900),

        );
        DB::table('admins_screen_list')->insert(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > CRM > CSAT Formula Setting', 'url'=>'admin.settings.csat_cases_setting.formula.index', 'permission_id' => 902)

        );
    }
}
