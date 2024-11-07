<?php

use Illuminate\Database\Seeder;

class AgentListScreenPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 993, 'name' => 'RVR Caller Agents List - Edit Action', 'module_id' => 14),
            array('id' => 994, 'name' => 'RVR Caller Agents List - Bulk Update Agent Type Action', 'module_id' => 14),
        ));
    }
}
