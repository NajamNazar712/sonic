<?php

use App\Http\Controllers\CargoManifestBagJourneyController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\CargoManifest\CargoManifest;
use App\Http\Models\Admin\CargoManifest\ManifestBag;
use App\Http\Models\Admin\CargoManifest\V2JunctionMapping;
use App\Http\Models\Shipment;
use Illuminate\Database\Seeder;
use App\Http\Models\Admin\CargoManifest\CargoManifestBag;
use Illuminate\Support\Facades\Auth;

class CargoManifestBagsFixProduction extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $seal_numbers = ['14430011513169', '14430011519594', '1039089', '1039086', '1033717', '1039080', '1024078', '1024076', '1024045', '1024044', '1024043', '22314411512587', '22314411512588', '22314411512589', '22314411512585', '22314411512586', '22314411538478', '22314411538499', '22314411538458', '22314411538461', '1024073', '1024830', '22328811512742', '22328811512754', '22328811512757', '22328811512762', '22328811512733', '22328811512771', '22328811512743', '22328811512760', '22328811512761', '22328811538464', '22328811538501', '22318611490204', '22318611490205', '22318611490206', '1036108', '1024079', '1024048', '22325111490287', '22325111490285', '22325111490303', '22325111512702', '22325111512694', '22325111512697', '22325111512700', '22325111512710', '22325111512704', '22325111512699', '22325111538480', '22325111538500', '1024091', '1036078', '1024821', '1036449', '1036464', '22327111512728', '22327111512720', '22327111512725', '22327111512723', '22327111512724', '22327111512726', '327290122792', '327290122899', '327290122754', '327290122790', '327290122942', '327290122123', '327290122945', '327290122907', '22331511512827', '22331511538454', '22331511538462', '1039095', '1039092'];
        $pending_cargo_manifests = array();
        $pending_cargo_manifest_details = array();
        $bags = CargoManifestBag::whereIn('seal_number', $seal_numbers);
        if($bags->exists()){
            $bags = $bags->get();
            foreach ($bags as $bag){
                $pending_cargo_manifests[$bag->origin_hub_id][$bag->destination_hub_id][] = $bag->id;
                if(array_key_exists($bag->origin_hub_id, $pending_cargo_manifest_details)){
                    if(array_key_exists($bag->destination_hub_id, $pending_cargo_manifest_details[$bag->origin_hub_id])){
                        $pending_cargo_manifest_details[$bag->origin_hub_id][$bag->destination_hub_id]['shipments'] = $pending_cargo_manifest_details[$bag->origin_hub_id][$bag->destination_hub_id]['shipments'] + $bag->shipments;
                        $pending_cargo_manifest_details[$bag->origin_hub_id][$bag->destination_hub_id]['bags_weight'] = $pending_cargo_manifest_details[$bag->origin_hub_id][$bag->destination_hub_id]['bags_weight'] + $bag->shipments_weight;
                    }
                    else{
                        $pending_cargo_manifest_details[$bag->origin_hub_id][$bag->destination_hub_id]['shipments'] = $bag->shipments;
                        $pending_cargo_manifest_details[$bag->origin_hub_id][$bag->destination_hub_id]['bags_weight'] = $bag->shipments_weight;
                    }
                }
                else{
                    $pending_cargo_manifest_details[$bag->origin_hub_id][$bag->destination_hub_id]['shipments'] = $bag->shipments;
                    $pending_cargo_manifest_details[$bag->origin_hub_id][$bag->destination_hub_id]['bags_weight'] = $bag->shipments_weight;
                }
            }
            foreach ($pending_cargo_manifests as $origin_id => $pendings_destinations){
                foreach ($pendings_destinations as $destination_id => $bag_ids){
                    $master_cargo = new CargoManifest();

                    $master_cargo->origin_hub_id = $origin_id;
                    $master_cargo->destination_hub_id = $destination_id;
                    $master_cargo->shipping_mode_id = 1;
                    $master_cargo->transport_mode_id = 2;

                    $master_cargo->bags = $bags;
                    $master_cargo->shipments = $pending_cargo_manifest_details[$origin_id][$destination_id]['shipments'];
                    $master_cargo->bags_weight = $pending_cargo_manifest_details[$origin_id][$destination_id]['bags_weight'];
                    $master_cargo->actual_weight = $pending_cargo_manifest_details[$origin_id][$destination_id]['bags_weight'];
                    $master_cargo->created_by = 787;
                    $master_cargo->received_by = 787;

                    $master_cargo->vehicle_type = 2;
                    $master_cargo->vehicle_number = 'XYZ-123';

                    $master_cargo->driver_name = 'Global';
                    $master_cargo->driver_phone = '0300-0000000';
                    $master_cargo->vendor_name = 'Global';

                    $master_cargo->status_id = 2;
                    $master_cargo->save();

                    $master_cargo_id = $master_cargo->id;

                    foreach ($bag_ids as $bag_id) {
                        $mater_cargo_bags= new ManifestBag();

                        $mater_cargo_bags->cargo_manifest_id = $master_cargo_id;
                        $mater_cargo_bags->cargo_manifest_bag_id = $bag_id;

                        $mater_cargo_bags->save();

                        $bag = CargoManifestBag::find($bag_id);

                        if($bag->junction_mapping_id == null) {
                            $mapping = V2JunctionMapping::where([['origin_id', $origin_id],['destination_id',$destination_id],['status',1]])->first();
                            if($mapping){
                                $bag->junction_mapping_id = $mapping->id;
                                $master_cargo->junction_mapping_id = $mapping->id;
                                $master_cargo->update();
                            }
                        }
                        else{
                            $master_cargo->junction_mapping_id = $bag->junction_mapping_id;
                            $master_cargo->update();
                        }

                        CargoManifestBagJourneyController::add($bag->id, $bag->seal_number, $bag->status_id, 787, $master_cargo_id, 1);
                    }
                }
            }
        }
    }
}
