<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityTrailForShipmentReversalReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 803, 'screen_name' => 'Shipment Reversal Report', 'action'=> 'View'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Shipment Reversal Report', 'url'=>'admin.reports.shipment_reversal_report.index', 'permission_id' => 894),           
        ));
    }
}
