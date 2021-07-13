<?php

use Illuminate\Database\Seeder;

class UpdateScanningScreenLocationTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('shipment_scanning_screen_locations')->insert(array(
            array('id' => 20, 'name' => 'Quick Receive Bag Shipment(s)', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 21, 'name' => 'Quick Receiving', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 22, 'name' => 'Multiple Piece Shipment(s)', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 23, 'name' => 'Supply Chain Shipment On Hold', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 24, 'name' => 'Telenor Return Update', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 25, 'name' => 'Open Parcel History', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 26, 'name' => 'Create Handover Note', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 27, 'name' => 'Receive Handover Note', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
        ));
    }
}
