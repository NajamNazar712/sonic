<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AgentsListSeeder extends Seeder
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
            array('id' => 951, 'name' => 'RVR Caller Agents List - View', 'module_id' => 14),
        ));


        DB::table('activity_trail_actions')->insert(array(
            array('id' => 763, 'screen_name' => 'RVR Caller Agents List', 'action' => 'View'),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 764, 'screen_name' => 'RVR Caller Agents List', 'action' => 'Excel Download'),
        ));


        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile > Reason Validation > RVR Caller Agents List', 'url'=>'admin.settings.agents_list.index', 'permission_id' => 951),
        ));
    }
}
