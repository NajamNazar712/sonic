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
        $bags = CargoManifestBag::where('status_id', 7)->get();
        if($bags){
            foreach ($bags as $bag){
                $bag_shipments = $bag->shipment;
                if($bag_shipments){
                    $flag = true;
                    $total_shipments = 0;
                    foreach ($bag_shipments as $bag_shipment){
                        $shipment = $bag_shipment->shipment;
                        if($shipment->shipper_status_id == 3 || $shipment->shipper_status_id == 21){
                            $flag = false;
                        }
                        $total_shipments++;
                    }
                    if($flag){
                        foreach ($bag_shipments as $bag_shipment){
                            $bag_shipment->status = 1;
                            $bag_shipment->save();
                        }
                        $bag->shipments = $total_shipments;
                        $bag->short_received_shipments = 0;
                        $bag->received_shipments = $total_shipments;
                        $bag->save();
                    }
                }
            }
        }
    }
}
