<?php

use Illuminate\Database\Seeder;

class CreateActivityTrailAndPermissionForCreatedShipmentReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 922, 'name' => 'Created Shipment vs Unpicked Shipment - Report', 'module_id' => 9),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 726, 'screen_name' => 'Created Shipment vs Unpicked Shipment Report', 'action'=> 'View'),
            array('id' => 727, 'screen_name' => 'Created Shipment vs Unpicked Shipment Report', 'action'=> 'Excel'),

        ));

        // $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        // DB::table('admins_screen_list')->insert(array(
        //     array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Created Shipment vs Unpicked Shipment', 'url'=>'admin.reports.created_shipment.index', 'permission_id' => 922),           
        // ));
    }
}
