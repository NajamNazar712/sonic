<?php

use Illuminate\Database\Seeder;

class UpdatePermissionSeederForTrackShipmentPosition extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 615, 'screen_name' => 'Track Actual Shipment Position', 'action'=> 'View'),
            array('id' => 616, 'screen_name' => 'Track Actual Shipment Position', 'action'=> 'Excel'),
        ));

        DB::table('module_permissions')->insert(array(
            array('id' => 822, 'name' => 'Track Actual Shipment Position - View', 'module_id' => 19)
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support > Track Actual Shipment Position', 'url'=>'admin.tracking.shipment_position.track', 'permission_id' => 822));

    }
}
