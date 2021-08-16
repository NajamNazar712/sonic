<?php

use Illuminate\Database\Seeder;

class ActivityTrailAirwayBillHistory extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 418, 'screen_name' => 'Airway Bill Print History', 'action'=> 'View'),
        ));
        DB::table('module_permissions')->insert(array(
            array('id' => 567, 'name' => 'Airway Bill Print History - VIEW', 'module_id' => 19),
        ));
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support > Airway Bill Print History', 'url'=>'admin.airway_journey.index', 'permission_id' => 567));

        DB::table('shipment_scanning_screen_locations')->insert(array(
            array('id' => 417, 'name' => 'Airway Bill Print History'),
        ));
    }
}
