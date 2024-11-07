<?php

use Illuminate\Database\Seeder;

class ActiviyTrailForReturnShipments extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 652, 'screen_name' => 'Return Shipments', 'action'=> 'View'),
            array('id' => 653, 'screen_name' => 'Return Shipments', 'action'=> 'Excel Download'),
          
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile > Return > Return Shipments', 'url'=>'admin.return.return_shipments.index', 'permission_id' => 860),           
        ));
    }
}
