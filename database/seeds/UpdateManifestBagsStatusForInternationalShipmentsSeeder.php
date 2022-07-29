<?php

use App\Http\Models\Admin\CargoManifest\CargoManifestBag;
use App\Http\Models\Admin\CargoManifest\CargoManifestBagShipments;
use Illuminate\Database\Seeder;

class UpdateManifestBagsStatusForInternationalShipmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bags = CargoManifestBag::where('status_id', 1)->whereHas('destination_hub', function ($query) {
                $query->where('business_category_id',2);
            });
        $cargo_manifest_bags = $bags->get();
        foreach($cargo_manifest_bags as $bags){
           $bag_shipments = CargoManifestBagShipments::where('cargo_manifest_bag_id',$bags->id);
           if($bag_shipments->exists()){
               $bag_shipments = $bag_shipments->get();
               foreach($bag_shipments as $bag_shipment){
                    $shipment = Shipment::where('id',$bag_shipment->shipment_id)->first();
                    if(in_array($shipment->shipper_status_id,[14,25])){
                        $bag_shipment->status = 1;
                        $bag_shipment->save();
                    }
               }
               $received_shipments_count = CargoManifestBagShipments::where('cargo_manifest_bag_id',$bags->id)->where('status',1)->count();
               if($bags->shipments == $received_shipments_count){
                  $bags->received_shipments = $received_shipments_count;
                  $bags->completed = 1;
                  $bags->save();
               }
           }
        }

    }
}
