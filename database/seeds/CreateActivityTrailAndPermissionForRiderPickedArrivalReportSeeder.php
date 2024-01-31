<?php

use Illuminate\Database\Seeder;

class CreateActivityTrailAndPermissionForRiderPickedArrivalReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 923, 'name' => 'Rider Picked Vs Arrival Shipments - Report', 'module_id' => 9),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 728, 'screen_name' => 'Rider Picked Vs Arrival Shipments Report', 'action'=> 'View'),
            array('id' => 729, 'screen_name' => 'Rider Picked Vs Arrival Shipments Report', 'action'=> 'Excel'),

        ));

        // $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        // DB::table('admins_screen_list')->insert(array(
        //     array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Rider Picked Vs Arrival Shipments', 'url'=>'admin.reports.pickup_arival.index', 'permission_id' => 923),           
        // ));
    }
}
