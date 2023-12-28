<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateScanningScreenLocationForRiderPickedStatus extends Seeder
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
            array('id'=>31,'name' => 'Rider Picked', 'created_at'=>$timestamp,'updated_at'=>$timestamp),
        ));
    }
}
