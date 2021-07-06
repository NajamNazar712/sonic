<?php

use Illuminate\Database\Seeder;

class ActivityTrailScreens extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 233, 'screen_name' => 'Supervisor Dashboard', 'action'=> 'View'),
            array('id' => 234, 'screen_name' => 'Supervisor Dashboard', 'action'=> 'Excel Download'),
            array('id' => 235, 'screen_name' => 'Agent Performance Screen', 'action'=> 'View'),
            array('id' => 236, 'screen_name' => 'Agent Performance Screen', 'action'=> 'Excel Download'),
            array('id' => 237, 'screen_name' => 'Caller Agent Screen', 'action'=> 'View'),
            array('id' => 238, 'screen_name' => 'Return Confirmation Pending TAT Setting', 'action'=> 'View'),
            array('id' => 239, 'screen_name' => 'Retail Sales Report', 'action'=> 'View'),
            array('id' => 240, 'screen_name' => 'Retail Sales Report', 'action'=> 'Excel Download'),
            array('id' => 241, 'screen_name' => 'Rider Incentive Daily Cron Time', 'action'=> 'View'),
            array('id' => 242, 'screen_name' => 'Riders Incentive', 'action'=> 'View'),
            array('id' => 243, 'screen_name' => 'Riders Incentive', 'action'=> 'Excel Download'),
            array('id' => 244, 'screen_name' => 'Riders Incentive Setting', 'action'=> 'View'),
            array('id' => 245, 'screen_name' => 'Riders Incentive Setting', 'action'=> 'Excel Download'),
            array('id' => 246, 'screen_name' => 'Fleet Management', 'action'=> 'View'),
            array('id' => 247, 'screen_name' => 'Fleet Management', 'action'=> 'Excel Download'),
            array('id' => 248, 'screen_name' => 'Route Management', 'action'=> 'View'),
            array('id' => 249, 'screen_name' => 'Route Management', 'action'=> 'Excel Download'),
            array('id' => 250, 'screen_name' => 'Scanning History', 'action'=> 'View'),
            array('id' => 251, 'screen_name' => 'Runners', 'action'=> 'View'),
            array('id' => 252, 'screen_name' => 'Runners', 'action'=> 'Excel Download'),
            array('id' => 253, 'screen_name' => 'Vehicle In Transit', 'action'=> 'View'),
            array('id' => 254, 'screen_name' => 'Vehicle In Transit', 'action'=> 'Excel Download'),
            array('id' => 255, 'screen_name' => 'Shipper Insurance Report', 'action'=> 'View'),
            array('id' => 256, 'screen_name' => 'Shipper Insurance Report', 'action'=> 'Excel Download'),
            array('id' => 257, 'screen_name' => 'ERF Dashboard', 'action'=> 'View'),
            array('id' => 258, 'screen_name' => 'ERF Dashboard', 'action'=> 'Excel Download'),
            array('id' => 259, 'screen_name' => 'FTL Request', 'action'=> 'View'),
            array('id' => 260, 'screen_name' => 'FTL Request', 'action'=> 'Excel Download'),
            array('id' => 261, 'screen_name' => 'FTL Invoices', 'action'=> 'View'),
            array('id' => 262, 'screen_name' => 'FTL Invoices', 'action'=> 'Excel Download'),
            array('id' => 263, 'screen_name' => 'Add ERF', 'action'=> 'View'),
            array('id' => 264, 'screen_name' => 'Economy Rates (Edit)', 'action'=> 'View'),
            array('id' => 265, 'screen_name' => 'Economy Rates (View)', 'action'=> 'View'),
            array('id' => 266, 'screen_name' => 'Telenor Bulk Return', 'action'=> 'View'),
            array('id' => 267, 'screen_name' => 'Work Code Master Report', 'action'=> 'View'),
            array('id' => 268, 'screen_name' => 'Work Code Master Report', 'action'=> 'Excel Download'),
            array('id' => 269, 'screen_name' => 'DN ByPass Request', 'action'=> 'View'),
            array('id' => 270, 'screen_name' => 'DN ByPass Request', 'action'=> 'Excel Download'),
            array('id' => 271, 'screen_name' => 'Tracking', 'action'=> 'View'),
            array('id' => 272, 'screen_name' => 'Quick Tracking', 'action'=> 'View'),
        ));
    }
}
