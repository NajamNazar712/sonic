<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Http\Models\ShipmentStatus;

class UpdateRvStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   

        $timestamp = \Carbon\Carbon::now();
        
        DB::table('shipment_status')->insert(array(
            array('id'=> 65 ,'code' => 'R-SAR', 'name' => 'Shipment - Shipper Advise Requested','description'=> 'Shipments will be shown to the shipper ', 'created_at' => $timestamp,'updated_at' => $timestamp),
            array('id' => 66, 'code' => 'S-RCR', 'name' => 'Shipment - Re-Attempt Call Requested', 'description' => 'Shipment is requested to be re-attempt for call by the shipper', 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));

        ShipmentStatus::where('id', 12)->update(['code' => 'R-VR', 'name' => 'Shipment - Reason Validation Required', 'updated_at' => $timestamp]);
    }
}
