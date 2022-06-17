<?php

use Illuminate\Database\Seeder;

class UpdateActivityTrailForInvoiceAgainstDeliveredShipper extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 532, 'screen_name' => 'Invoice Against Return Delivered Shipment', 'action'=> 'View'),
        ));
    }
}
