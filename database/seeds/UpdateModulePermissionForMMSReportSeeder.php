<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForMMSReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 566, 'screen_name' => 'MMS Report', 'action'=> 'View'),
            array('id' => 567, 'screen_name' => 'MMS Report', 'action'=> 'Excel Download'),
        ));
        DB::table('module_permissions')->insert(array(
            array('id' => 780, 'name' => 'Vigilance Verification - View', 'module_id' => 9),
        ));
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > MMS', 'url'=>'admin.reports.mms.index', 'permission_id' => 780)
        );
    }
}
