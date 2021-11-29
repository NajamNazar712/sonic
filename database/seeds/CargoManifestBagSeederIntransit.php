<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Admin\CargoManifest\CargoManifestBag;

class CargoManifestBagSeederIntransit extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = \Carbon\Carbon::parse('16-11-2021 18:00:00')->toDateTimeString();
        $bags = CargoManifestBag::where('created_at' ,'<=', $date)->get();
        if($bags){
            foreach ($bags as $bag){
                $bag_shipments = $bag->shipment;
                if($bag_shipments){
                    $flag = false;
                    $total_shipments = 0;
                    $unreceived_shipments = 0;
                    foreach ($bag_shipments as $bag_shipment){
                        $shipment = $bag_shipment->shipment;
                        if($shipment->shipper_status_id == 3 || $shipment->shipper_status_id == 21 ||  $shipment->shipper_status_id == 26 || $shipment->shipper_status_id == 32 || $shipment->shipper_status_id == 49){
                            $flag = true;
                            $unreceived_shipments++;

                            $bag_shipment->status = 0;
                            $bag_shipment->save();
                        }
                        $total_shipments++;
                    }
                    if($flag){
                        $bag->shipments = $total_shipments;
                        if($total_shipments !=  $unreceived_shipments){
                            $bag->short_received_shipments = $unreceived_shipments;
                        }

                        $bag->received_shipments = $total_shipments - $unreceived_shipments;
                        $bag->save();
                    }
                }
            }
        }
    }
}
