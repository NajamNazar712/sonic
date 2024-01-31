<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForRvAssignedAgentShipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 927, 'name' => 'Rv Assigned Agent Shipments', 'module_id' => 25),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 734, 'screen_name' => 'Rv Assigned Agent Shipments', 'action'=> 'View'),
          
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Leads > Rv Assigned Agent Shipments', 'url'=>'admin.rv_assign_shipments.index', 'permission_id' => 927),           
        ));
    }
}
