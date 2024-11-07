<?php

use Illuminate\Database\Seeder;

class ActiviyTrailForDeliveryShipments extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 650, 'screen_name' => 'Delivery Shipments', 'action'=> 'View'),
            array('id' => 651, 'screen_name' => 'Delivery Shipments', 'action'=> 'Excel Download'),
          
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile > Delivery > Delivery Shipments', 'url'=>'admin.delivery.delivery_shipments.index', 'permission_id' => 859),           
        ));
    }
}
