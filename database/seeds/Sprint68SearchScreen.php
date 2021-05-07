<?php

use Illuminate\Database\Seeder;

class Sprint68SearchScreen extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile > Debriefing > Supervisor Dashboard', 'url'=>'admin.debriefing.supervisor.index', 'permission_id' => 495),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile > Debriefing > Agent Performance', 'url'=>'admin.debriefing.agents_call_monitoring.index', 'permission_id' => 496),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile > Debriefing > Caller Agent', 'url'=>'admin.debriefing.caller_agent.index', 'permission_id' => 497),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > HR > Rider Incentive', 'url'=>'admin.settings.hr.rider_incentive.index', 'permission_id' => 491),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > HR > Rider Incentive Cron', 'url'=>'admin.settings.hr.rider_incentive.cron.index', 'permission_id' => 494),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Last Mile > Return Confirmation Pending TAT', 'url'=>'admin.settings.rcp_tat.index', 'permission_id' => 488),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Retail Sales', 'url'=>'admin.reports.retail_sales.index', 'permission_id' => 493),
        ));
    }
}
