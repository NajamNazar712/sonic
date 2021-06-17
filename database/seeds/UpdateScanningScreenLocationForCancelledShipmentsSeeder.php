<?php

use Illuminate\Database\Seeder;

class UpdateScanningScreenLocationForCancelledShipmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_scanning_screen_locations')->truncate();
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('shipment_scanning_screen_locations')->insert(array(
            array('id' => 16, 'name' => 'Cancelled Shipment Add', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
            array('id' => 17, 'name' => 'International Shipment Status Update', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
        ));
    }
}
