<?php

use Illuminate\Database\Seeder;

class UpdatePermissionsForShipperSummaryReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 794, 'name' => 'Shipper Summary Report - View', 'module_id' => 9)
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 583, 'screen_name' => 'Shipper Summary Report', 'action'=> 'View'),
            array('id' => 584, 'screen_name' => 'Shipper Summary Report', 'action'=> 'Excel'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Shipper Summary', 'url'=>'admin.reports.shipper_summary.index', 'permission_id' => 794));

        DB::table('admin_role_module_permissions')->insert(array(
            array('role_id' => 44, 'permission_id' => 794),
            array('role_id' => 4, 'permission_id' => 794),
        ));
    }
}
