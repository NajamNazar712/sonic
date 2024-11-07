<?php

use Illuminate\Database\Seeder;

class NpsSurveyModulePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        548,549
        DB::table('module_permissions')->insert(array(
            array('id' => 751, 'name' => 'NPS SURVEY - View', 'module_id' => 31),
            array('id' => 752, 'name' => 'NPS Add Survey Add - View', 'module_id' => 31),
            array('id' => 753, 'name' => 'NPS Survey Edit - Action', 'module_id' => 31),
            array('id' => 754, 'name' => 'NPS Survey Status - Action', 'module_id' => 31),
            array('id' => 755, 'name' => 'NPS Survey Response Report - View', 'module_id' => 31),
            array('id' => 756, 'name' => 'NPS Survey Consolidated Report - View', 'module_id' => 31),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 548, 'screen_name' => 'NPS SURVEY', 'action'=> 'View'),
            array('id' => 549, 'screen_name' => 'NPS SURVEY Add', 'action'=> 'View'),
            array('id' => 550, 'screen_name' => 'NPS SURVEY Response Report', 'action'=> 'View'),
            array('id' => 551, 'screen_name' => 'NPS SURVEY Consolidated Report', 'action'=> 'View'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Quality Assurance > Nps Survey', 'url'=>'admin.nps.index', 'permission_id' => 751),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Quality Assurance > Nps Survey Add', 'url'=>'admin.nps.add', 'permission_id' => 752),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Quality Assurance > Nps Survey Response Report', 'url'=>'admin.nps.response.report', 'permission_id' => 755),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Quality Assurance > Nps Survey Consolidated Report', 'url'=>'admin.nps.consolidate.report', 'permission_id' => 756),
        ));


    }
}
