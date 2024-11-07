<?php

use Illuminate\Database\Seeder;

class RcpAgentCnPermissionsSeeder extends Seeder
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
            array('id' => 876, 'name' => 'RCP Agent CN - View', 'module_id' => 7),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 665, 'screen_name' => 'RCP Agent CN', 'action'=> 'View'),
            array('id' => 666, 'screen_name' => 'RCP Agent CN', 'action'=> 'Excel Download'),
        ));

        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile > Return > RCP Agent Productivity Shipment Wise', 'url'=>'admin.return.rcp_agent_cn.index', 'permission_id' => 876),
        ));
    }
}
