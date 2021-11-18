<?php

use Illuminate\Database\Seeder;

class UpdateActivityTrailSeederForSalesIncentive extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 458, 'screen_name' => 'Sales Incentive - Territory', 'action'=> 'View'),
            array('id' => 459, 'screen_name' => 'Sales Incentive - Designation', 'action'=> 'View'),
            array('id' => 460, 'screen_name' => 'Sales Incentive - Incentive Settings', 'action'=> 'View'),
            array('id' => 461, 'screen_name' => 'Sales Incentive - Report', 'action'=> 'View'),
            array('id' => 462, 'screen_name' => 'Sales Incentive - Consolidated Report', 'action'=> 'View'),
            array('id' => 463, 'screen_name' => 'Sales Incentive - Territory', 'action'=> 'Excel Download'),
            array('id' => 464, 'screen_name' => 'Sales Incentive - Designation', 'action'=> 'Excel Download'),
            array('id' => 467, 'screen_name' => 'Sales Incentive - Report', 'action'=> 'Excel Download'),
            array('id' => 468, 'screen_name' => 'Sales Incentive - Consolidated Report', 'action'=> 'Excel Download'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Sales > Sales Territory', 'url'=>'admin.sales.territory.territoryindex', 'permission_id' => 610),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Sales > Sales Designation ', 'url'=>'admin.sales.designation.designationindex', 'permission_id' => 611),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Sales > Incentive Settings', 'url'=>'admin.settings.sales.incentive.index', 'permission_id' => 612),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Sales Incentive', 'url'=>'admin.reports.sales_incentive.index', 'permission_id' => 613),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Consolidated Sales Incentive', 'url'=>'admin.reports.sales_incentive.consolidated', 'permission_id' => 614)
        ));
    }
}
