<?php

use App\Http\Models\Admin\CargoManifest\CargoManifestBag;
use App\Http\Models\Admin\CargoManifest\CargoManifestBagShipments;
use Illuminate\Database\Seeder;

class UpdateManifestBagShipmentsForDispute extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bags = CargoManifestBag::where('status_id', 8)->whereHas('destination_hub', function ($query) {
            $query->where('business_category_id',1);
        });
        $cargo_manifest_bags = $bags->get();
        foreach($cargo_manifest_bags as $bags) {
            $bag_shipments = CargoManifestBagShipments::where('cargo_manifest_bag_id', $bags->id);
            if ($bag_shipments->exists()) {
                $bag_shipments = $bag_shipments->get();
                $not_received_count = CargoManifestBagShipments::where('cargo_manifest_bag_id', $bags->id)->where('status', 0)->count();
                if ($not_received_count == 0) {
                    foreach ($bag_shipments as $bag_shipment) {
                        $shipment = Shipment::where('id', $bag_shipment->shipment_id)->first();
                        if ($shipment->shipper_status_id == 49) {
                            $mis_fwd = ShipmentsJourney::where('shipment_id',$shipment->id)->where('shipper_status_id',49)->orderBy('id','desc')->first();
                            if($mis_fwd){
                                $mis_fwd->delete();
                            }
                            $mis = ShipmentsJourney::where('shipment_id',$shipment->id)->where('shipper_status_id',11)->orderBy('id','desc')->first();
                            if($mis){
                                $mis->delete();
                            }
                            $journey = ShipmentsJourney::where('shipment_id',$shipment->id)->orderBy('id','desc')->first();
                            $shipment->shipper_status_id = $journey->shipper_status_id;
                            $shipment->shipper_status_id= $journey->consignee_status_id;
                            $shipment->save();
                        }

                    }
                    $bags->status_id = 7;
                    $bags->save();

                }

                }

            }
        }
    }
