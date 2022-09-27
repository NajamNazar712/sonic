<?php

use App\Http\Models\Admin\CargoManifest\CargoManifestBag;
use App\Http\Models\Admin\CargoManifest\CargoManifestBagShipments;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use Illuminate\Database\Seeder;

class DeleteReturnConfirmBagsWithSameOriginAndDestination extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $trackings = array(22322318558833,22322318567776,22322318615853,22322318626706,22322318652152,22350318659257,22322318663997,22322318673030,22322318697564,22350318726821,22322318734412,22322318787247,22322318808960,22322318498051,22322318577041,22322318665570,22350318807982,22322317971639,22322318537567,22322318673789,22322318729898,22322318777678,485241259778,22322318781309,22322318781313,22322318791003,22350318807995,22322318811482,22322318818445,22322318485716,22322318542360,22322318616690,22322318665005,22322318738887,22322318747533,22322318777666,22322318781520,22322318808791,22322318466466,22322318624227,22322318431397,22322318689251,22322318629176,22322317980011,22350318413408,22322318568762,22322318571561,22322318573672,22322318492745,22350318570563,22322318632416,22322318727795,22322318727971,22322318758957,22322318818978,22322318822120,22322318829558,22322318865986,485241261816,22322318939011,485241262069,22322318965655,485241262365,22322318972582,22350318983068,485241262658,22322318491404,22322318494227,22322318499719,22322318527558,22322318528942,22322318577233,22322318598291,22322318617836
            );

        $bag_array = array();
        foreach ($trackings as $tracking){
            $shipment = Shipment::where('tracking_number',$tracking)->first();
            if($shipment){
                if($shipment->shipper_status_id == 22 || $shipment->shipper_status_id == 21){
                    $return_arrived_at_origin = ShipmentsJourney::where('shipment_id',$shipment->id)->where('shipper_status_id',22)->orderBy('id','desc')->first();
                    if($return_arrived_at_origin){
                        $return_arrived_at_origin->delete();
                    }
                    $return_in_transit = ShipmentsJourney::where('shipment_id',$shipment->id)->where('shipper_status_id',21)->orderBy('id','desc')->first();
                    if($return_in_transit){
                        $return_in_transit->delete();
                    }
                    $journey = ShipmentsJourney::where('shipment_id',$shipment->id)->orderBy('id','desc')->first();
                    Shipment::where('id',$shipment->id)->update(['shipper_status_id' => $journey->shipper_status_id,'consignee_status_id' => $journey->consignee_status_id ]);
                }

                $bag_shipment = CargoManifestBagShipments::where('shipment_id',$shipment->id)->latest()->first();
                if($bag_shipment){
                    $bag_id = $bag_shipment->cargo_manifest_bag_id;
                    $bag = CargoManifestBag::find($bag_id);
                    if($bag->type == 2){
                        if(!in_array($bag->id,$bag_array)){
                            array_push($bag_array,$bag_id);
                        }
                    }
                }
            }
        }

        if(count($bag_array) > 0){
            foreach($bag_array as $bag_id){
                CargoManifestBagShipments::where('cargo_manifest_bag_id',$bag_id)->delete();
                CargoManifestBag::where('id',$bag_id)->delete();
            }
        }


    }
}
