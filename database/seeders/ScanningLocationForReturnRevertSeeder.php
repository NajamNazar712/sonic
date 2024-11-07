<?php

use Illuminate\Database\Seeder;

class ScanningLocationForReturnRevertSeeder extends Seeder
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
            array('id' => 29, 'name' => 'Return Revert', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
        ));
    }
}
