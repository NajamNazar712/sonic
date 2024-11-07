<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForRevenueReportInvoiceWiseSeeder extends Seeder
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
            array('id' => 839, 'name' => 'Revenue Report By Invoice', 'module_id' => 9)
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 637, 'screen_name' => 'Revenue Report By Invoice', 'action'=> 'View'),
            array('id' => 638, 'screen_name' => 'Revenue Report By Invoice', 'action'=> 'Excel'),
        ));

        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Revenue Report By Invoice', 'url'=>'admin.reports.revenue_report_by_invoice.index', 'permission_id' => 839),
        ));
    }
}
