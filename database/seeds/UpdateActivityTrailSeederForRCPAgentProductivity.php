<?php

use Illuminate\Database\Seeder;

class UpdateActivityTrailSeederForRCPAgentProductivity extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 451, 'screen_name' => 'RCP Agent Productivity', 'action'=> 'View'),
            array('id' => 452, 'screen_name' => 'RCP Agent Productivity', 'action'=> 'Excel Download'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile > Return > RCP Agent Productivity', 'url'=>'return.rcp_agent.index', 'permission_id' => 600)
        );
    }
}
