<?php

use Illuminate\Database\Seeder;

class OverlandReportPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('module_permissions')->insert(array(
            array('id' => 886, 'name' => 'Overland Report - View', 'module_id' => 9),
        ));
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 679, 'screen_name' => 'Overland Report', 'action'=> 'View'),
        ));
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp,
                'name' => 'Reports > Overland Report',
                'url'=>'admin.reports.overland.index', 'permission_id' => 886),
        ));

        DB::table('admin_role_module_permissions')->insert(array(
            array('role_id' => 3, 'permission_id' => 886),
            array('role_id' => 15, 'permission_id' => 886),
            array('role_id' => 93, 'permission_id' => 886),
        ));
    }
}
