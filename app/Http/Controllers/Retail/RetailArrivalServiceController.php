<?php

namespace App\Http\Controllers\Retail;

use App\Helpers\PayfastApiCall;
use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Controllers\Admins\Handover\HandoverShipmentJourneyController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EmployeeAttendanceController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentOpenBoxJourneyController;
use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\CancelledShipmentArrival;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\Retail\RetailShipment;
use App\Http\Models\Admin\RiderCategoryByPass;
use App\Http\Models\Handover\Handover;
use App\Http\Models\Handover\HandoverShipments;
use App\Http\Models\Notification;
use App\Http\Models\ProjectArrivalShipper;
use App\Http\Models\Rider;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentDetail;
use App\Http\Models\ShipmentItem;
use App\Http\Models\ShipmentOtp;
use App\Http\Models\ShipmentPiece;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipmentsWeightType;
use App\Http\Models\V2Pickup\V2PickupRequest;
use App\Http\Models\V2Pickup\V2PickupRequestShipment;
use App\Http\Models\WeightType;
use App\Jobs\CountFintechCharges;
use App\Models\PudoDeliverShipment;
use App\Models\PudoPickupShipment;
use App\Models\TransferNote;
use App\Models\TransferNoteShipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RetailArrivalServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:retail');
    }

    public function arrival_service_index(){

        return view('retail.arrival_service_center.arrival_service');
    }
    public function arrival_service_details(Request $request){

        $shipment = Shipment::where('tracking_number',$request->tracking_number);

        if($shipment->exists()) {

            $shipment = $shipment->first();

            $pudo_pickup = PudoPickupShipment::where('shipment_id',$shipment->id)->first();
            if(!$pudo_pickup){
                return ['status' => 1, 'error' => 'This shipment is not eligible for pickup drop-off!'];
            }


            //todo: now checking canceled shipment arrival
            $user = ShipmentsJourney::where('shipment_id',$shipment->id)->select('user_id','shipper_status_id')->orderby('id','desc')->first();

            if($user->shipper_status_id == 17)
            {
                $canceled_shipment = CancelledShipmentArrival::where('shipper_id',$user->user_id)->first();
                if($canceled_shipment)
                {
                    return ['status' => 1, 'error' => 'Shipment is not allowed for arrival because shipper cancelled this shipment !'];
                }
            }

            $amount = NULL;

            if(session('role_id') != 1){
                if($pudo_pickup->hub_id) {
                    if($pudo_pickup->hub_id != session('hub_id')){
                        return ['status' => 1, 'error' => 'You can not do arrival of this hub\'s shipment'];
                    }
                } else {
                    $shipment_origin = $shipment->pickup_address->city->hub_id;
                    if($shipment_origin != session('hub_id')){
                        return ['status' => 1, 'error' => 'You can not do arrival of this hub\'s shipment'];
                    }
                } 
            }

            if ($shipment->shipper_status_id == 1) {
                if($shipment->booking_type_id == 3){
                    $details = array();
                    $shipment_items = ShipmentItem::where('shipment_id', $shipment->id)->pluck('id')->toArray();
                    $shipment_items_count = count($shipment_items);

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['shipment_items'] = $shipment_items;
                    $details['shipment_items_count'] = $shipment_items_count;


                    ShipmentScanningJourneyController::add($shipment->id ,32,4,Auth::id(),NULL,NULL,NULL,NULL, NULL, NULL, 1);

                    return ['status' => 2, 'success' => 'Try and Buy Shipment found!', 'details' => $details];
                }else if($shipment->booking_type_id == 1 && $shipment->pieces > 1){
                    $details = array();
                    $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['pieces_count'] = $shipment->pieces;
                    $details['pieces_tracking_numbers'] = $shipment_pieces;
                    ShipmentScanningJourneyController::add($shipment->id ,32,4,Auth::id(),NULL,NULL,NULL,NULL, NULL, NULL, 1);
                    return ['status' => 3, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                }
                else{
//                    if($shipment->shipper_status_id == 17){
//                        AdminPickupsController::generate($shipment->id);
//                    }
//
//                    $shipment->save();

                    $details = array();
                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['city'] = $shipment->consignee_city->name;
                    $details['hub'] = $shipment->consignee_city->hub_city->name;
                    $details['shipper'] = $shipment->user->name;
                    $details['amount'] = $shipment->amount;
                    //$details['weight'] = floatval($shipment->actual_weight);

                    ShipmentScanningJourneyController::add($shipment->id ,32,4,Auth::id(),NULL,NULL,NULL,NULL, NULL, NULL, 1);
                    return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
                }
            } else {
                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
            }
        }
        else {
            $shipment_item = ShipmentItem::find($request->tracking_number);
            $shipment_id   = $shipment_item->shipment_id ?? null;

            $pudo_pickup = PudoPickupShipment::where('shipment_id', $shipment_id)->first();

            if ($shipment_item && !$pudo_pickup) {
                return ['status' => 1, 'error' => 'This shipment is not eligible for pickup drop-off!'];
            }

            if($shipment_item){

                if($pudo_pickup->hub_id != session('hub_id')){
                    return ['status' => 1, 'error' => 'You can not do arrival of this hub\'s shipment'];
                }

                $shipment = Shipment::find($shipment_item->shipment_id);
                if($shipment->booking_type_id == 3) {
                    if ($shipment->shipper_status_id == 1) {
                        $details = array();
                        $shipment_items = ShipmentItem::where('shipment_id', $shipment->id)->pluck('id')->toArray();
                        $shipment_items_count = count($shipment_items);

                        $details['id'] = $shipment->id;
                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['shipment_items'] = $shipment_items;
                        $details['shipment_items_count'] = $shipment_items_count;
                        $details['scanned_shipment_item'] = $shipment_item->id;

                        ShipmentScanningJourneyController::add($shipment->id ,32,4,Auth::id(),NULL,NULL,NULL,NULL, NULL, NULL, 1);
                        return ['status' => 2, 'success' => 'Try and Buy Shipment found!', 'details' => $details];
                    } else {
                        return ['status' => 1, 'error' => 'Given Item ID/Tracking Number\'s Shipment has already been modified'];
                    }
                } else {
                    return ['status' => 1, 'error' => 'Given Tracking Number\'s is not try and buy'];
                }
            }
            else {
                $shipment_pieces = ShipmentPiece::where('tracking_number', $request->tracking_number)->first();
                $shipment_id   = $shipment_pieces->shipment_id ?? null;

                $pudo_pickup = PudoPickupShipment::where('shipment_id', $shipment_id)->first();

                if ($shipment_pieces && !$pudo_pickup) {
                    return ['status' => 1, 'error' => 'This shipment is not eligible for pickup drop-off!'];
                }

                if($shipment_pieces){
//                    $shipment_pieces = $shipment_pieces->first();
                    $shipment = Shipment::find($shipment_pieces->shipment_id);

                    if($pudo_pickup->hub_id != session('hub_id')){
                        return ['status' => 1, 'error' => 'You can not do arrival of this hub\'s shipment'];
                    }

                    if ($shipment->shipper_status_id == 1) {
                        $details = array();
                        $shipment_all_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();
                        $details['id'] = $shipment->id;
                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['pieces'] = $shipment->pieces;
                        $details['pieces_tracking_numbers'] = $shipment_all_pieces;
                        $details['scanned_shipment_piece'] = $shipment_pieces->tracking_number;
                        ShipmentScanningJourneyController::add($shipment->id ,32,4,Auth::id(),NULL,NULL,NULL,NULL, NULL, NULL, 1);
                        return ['status' => 3, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                    } else {
                        return ['status' => 1, 'error' => 'Given Item ID/Tracking Number\'s Shipment has already been modified'];
                    }
                }
            }
        }

        return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
    }
    public function service_arrival_submit(Request $request){
        //  dd($request);
        $shipment_ids = explode(',', $request->shipment_ids);


        $pickup_request_ids = array();

        $print_shipment_ids = array();

        foreach ($shipment_ids as $key => $shipment_id) {
            $shipment = Shipment::find($shipment_id);
            if($shipment){

                $pudo_pickup = PudoPickupShipment::where('shipment_id',$shipment->id)->first();
                if(!$pudo_pickup){
                    return ['status' => 1, 'error' => 'This shipment is not eligible for pickup drop-off!'];
                }

                if ($shipment->shipper_status_id == 1) {
                    $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id)->where('status', 0)->orderBy('id', 'DESC')->first();
                    if($pickup_request_shipment){
                        $reference_1_id = $pickup_request_shipment->pickup_request_id;

                        if (!in_array($pickup_request_shipment->pickup_request_id, $pickup_request_ids)) {
                            $pickup_request_ids[] = $pickup_request_shipment->pickup_request_id;
                        }
                    }
                    else{
                        $reference_1_id = NULL;
                    }

                    $shipment->shipper_status_id = 61;
                    $shipment->consignee_status_id = 61;
                    $shipment->save();
                    $reference_2_id = NULL;
                    ShipmentsJourneyController::add($shipment_id, 61, 61, NULL, NULL, NULL,NULL, $reference_1_id, $reference_2_id, 1, NULL, NULL, NULL, NULL, NULL,  Auth::id());

                    $shipment->refresh();
                }
            }else {
                unset($shipment_ids[$key]);
            }

        }

        if (empty($print_shipment_ids)) {
            return redirect()->back()->with(['success' => 'Arrival Done']);
        }
        else {
            return redirect()->back()->with(['success' => 'Arrival Done', 'print_shipment_ids' => $print_shipment_ids]);
        }

    }

    public function arrival_try_and_buy_shipment_details(Request $request)
    {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);
        if ($shipment->exists()) {
            $shipment = $shipment->first();

            $pudo_pickup = PudoPickupShipment::where('shipment_id',$shipment->id)->first();
            if(!$pudo_pickup){
                return ['status' => 1, 'error' => 'This shipment is not eligible for pickup drop-off!'];
            }

            $settings = GlobalSettings::where('type', 'global_rider_id')->first();

            if ($settings) {
                $global_rider_id = $settings->setting_value;
            } else {
                $global_rider_id = 0;
            }
//            || $shipment->shipper_status_id == 17 || $shipment->shipper_status_id == 53 || $shipment->shipper_status_id == 61 || $shipment->shipper_status_id == 62 || $shipment->shipper_status_id == 64
            if ($shipment->shipper_status_id == 1) {
                $rider_assigned_flag = false;
                if ($shipment->booking_type_id == 3) {
//                    if ($shipment->shipper_status_id == 17) {
//                        AdminPickupsController::generate($shipment->id);
//                    }
//                    $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id);
//                    if ($pickup_request_shipment->exists()) {
//                        $pickup_request_shipment = $pickup_request_shipment->latest('id')->first();
//                        $pickup_request_id = $pickup_request_shipment->pickup_request_id;
//                        $pickup_request = V2PickupRequest::find($pickup_request_id);
//                        if ($pickup_request->current_rider_id == null || $pickup_request->current_rider_id === $global_rider_id) {
//                            //                            $rider_id = $this->generate_trax_pickup($pickup_request_id);
//                            //                            $rider = Rider::find($rider_id)->name;
//                            $rider = '';
//                            $rider_assigned_flag = true;
//                        } else {
//                            $rider = $pickup_request->rider->name;
//                        }
//                    } else {
////                        AdminPickupsController::generate($shipment->id);
//
//                        $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id);
//                        if ($pickup_request_shipment->exists()) {
//                            $pickup_request_shipment = $pickup_request_shipment->latest('id')->first();
//                            $pickup_request_id = $pickup_request_shipment->pickup_request_id;
//                            $pickup_request = V2PickupRequest::find($pickup_request_id);
//                            if ($pickup_request->current_rider_id == null || $pickup_request->current_rider_id === $global_rider_id) {
//                                //                                $rider_id = $this->generate_trax_pickup($pickup_request_id);
//                                //                                $rider = Rider::find($rider_id)->name;
//                                $rider = '';
//                                $rider_assigned_flag = true;
//                            } else {
//                                $rider = $pickup_request->rider->name;
//                            }
//                        }
//                    }
                    $retail_shipment = RetailShipment::where('shipment_id', $shipment->id);
                    if ($retail_shipment->exists()) {
                        $shipment->actual_weight = $shipment->estimated_weight;
                    } else {
                        $weight_flag = true;
                        $project_arrival_include_shippers = ProjectArrivalShipper::where('user_id', $shipment->user_id);
                        if ($project_arrival_include_shippers->exists()) {
                            if ($shipment->actual_weight == null) {
                                $weight_flag = true;
                            } else {
                                $weight_flag = false;
                            }
                        }
                        if ($weight_flag == true) {
                            if ($request->has('weight')) {
                                $shipment->actual_weight = $request->weight;
                            }
                        }
                    }
                    $shipment->save();
                    if($request->has('weight_type')){
                        $shipment_weight = ShipmentsWeightType::where('shipment_id', $shipment->id);
                        if($shipment_weight->exists()){
                            $shipments_weight_type = $shipment_weight->first();
                        }else{
                            $shipments_weight_type = new ShipmentsWeightType;
                        }
                        $weight_type = WeightType::find($request->weight_type);
                        $shipments_weight_type->shipment_id = $shipment->id;
                        $shipments_weight_type->weight_type = $weight_type->id;
                        $shipments_weight_type->save();
                    }

                    $details = array();

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['shipper'] = $shipment->user->name;
//                    $details['pickup_request_id'] = str_pad($pickup_request->id, 6, '0', STR_PAD_LEFT);
//                    $details['rider'] = $rider;
                    $details['weight'] = floatval($shipment->actual_weight);
//                    $details['pickup_request_id_unpadded'] = $pickup_request_id;
//                    $details['rider_assigned'] = $rider_assigned_flag;
                    $details['city'] = $shipment->consignee_city->name;
                    $details['hub'] = $shipment->consignee_city->hub_city->name;

                    ShipmentScanningJourneyController::add($shipment->id ,32,4,Auth::id(),NULL,NULL,NULL,NULL, NULL, NULL, 1);

                    return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
                } else {
                    return ['status' => 1, 'error' => 'Given Tracking Number\'s is not try and buy'];
                }
            } else {
                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
        }
    }

    public function arrival_piece_details(Request $request)
    {
        $shipment_id = $request->shipment_id;
        $shipment_piece_id = $request->piece_id;

        $shipment_piece = ShipmentPiece::where('tracking_number', $shipment_piece_id);
        if ($shipment_piece->exists()) {
            $shipment_piece = $shipment_piece->first();
            if ($shipment_piece->shipment_id == $shipment_id) {
                $scanned_shipment_piece = $shipment_piece->tracking_number;
                ShipmentScanningJourneyController::add($shipment_id ,32,4,Auth::id(),NULL,NULL,NULL,NULL, NULL, NULL, 1);
                return ['status' => 0, 'success' => 'Shipment Piece found!', 'scanned_shipment_piece' => $scanned_shipment_piece];
            } else {
                return ['status' => 1, 'error' => 'Given Item ID does not belong here'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Shipment Item with given Item ID is present'];
        }
    }

    public function arrival_piece_shipment_details(Request $request)
    {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);
        if ($shipment->exists()) {
            $shipment = $shipment->first();

            $pudo_pickup = PudoPickupShipment::where('shipment_id',$shipment->id)->first();
            if(!$pudo_pickup){
                return ['status' => 1, 'error' => 'This shipment is not eligible for pickup drop-off!'];
            }

            $settings = GlobalSettings::where('type', 'global_rider_id')->first();

            if ($settings) {
                $global_rider_id = $settings->setting_value;
            } else {
                $global_rider_id = 0;
            }
            if ($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17 || $shipment->shipper_status_id == 53 || $shipment->shipper_status_id == 61 || $shipment->shipper_status_id == 62 || $shipment->shipper_status_id == 64) {
                if ($shipment->pieces > 1) {
//                    $rider_assigned_flag = false;
//                    if ($shipment->shipper_status_id == 17) {
//                        AdminPickupsController::generate($shipment->id);
//                    }
//                    $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id);
//                    if ($pickup_request_shipment->exists()) {
//                        $pickup_request_shipment = $pickup_request_shipment->latest('id')->first();
//                        $pickup_request_id = $pickup_request_shipment->pickup_request_id;
//                        $pickup_request = V2PickupRequest::find($pickup_request_id);
//                        if ($pickup_request->current_rider_id == null || $pickup_request->current_rider_id === $global_rider_id) {
//                            //                            $rider_id = $this->generate_trax_pickup($pickup_request_id);
//                            //                            $rider = Rider::find($rider_id)->name;
//                            $rider = '';
//                            $rider_assigned_flag = true;
//                        } else {
//                            $rider = $pickup_request->rider->name;
//                        }
//                    } else {
//                        AdminPickupsController::generate($shipment->id);
//
//                        $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id);
//                        if ($pickup_request_shipment->exists()) {
//                            $pickup_request_shipment = $pickup_request_shipment->latest('id')->first();
//                            $pickup_request_id = $pickup_request_shipment->pickup_request_id;
//                            $pickup_request = V2PickupRequest::find($pickup_request_id);
//                            if ($pickup_request->current_rider_id == null || $pickup_request->current_rider_id === $global_rider_id) {
//                                //                                $rider_id = $this->generate_trax_pickup($pickup_request_id);
//                                //                                $rider = Rider::find($rider_id)->name;
//                                $rider = '';
//                                $rider_assigned_flag = true;
//                            } else {
//                                $rider = $pickup_request->rider->name;
//                            }
//                        }
//                    }

                    $retail_shipment = RetailShipment::where('shipment_id', $shipment->id);
                    if ($retail_shipment->exists()) {
                        $shipment->actual_weight = $shipment->estimated_weight;
                    } else {
                        $weight_flag = true;
                        $project_arrival_include_shippers = ProjectArrivalShipper::where('user_id', $shipment->user_id);
                        if ($project_arrival_include_shippers->exists()) {
                            if ($shipment->actual_weight == null) {
                                $weight_flag = true;
                            } else {
                                $weight_flag = false;
                            }
                        }
                        if ($weight_flag == true) {
                            if ($request->has('weight')) {
                                $shipment->actual_weight = $request->weight;
                            }
                        }
                    }
                    $shipment->save();
                    if($request->has('weight_type')) {
                        $shipment_weight = ShipmentsWeightType::where('shipment_id', $shipment->id);
                        if($shipment_weight->exists()){
                            $shipments_weight_type = $shipment_weight->first();
                        }else{
                            $shipments_weight_type = new ShipmentsWeightType;
                        }
                        $weight_type = WeightType::find($request->weight_type);
                        $shipments_weight_type->shipment_id = $shipment->id;
                        $shipments_weight_type->weight_type = $weight_type->id;
                        $shipments_weight_type->save();
                    }

                    $details = array();

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['shipper'] = $shipment->user->name;
//                    $details['pickup_request_id'] = str_pad($pickup_request->id, 6, '0', STR_PAD_LEFT);
//                    $details['rider'] = $rider;
                    $details['amount'] = $shipment->amount;
                    $details['weight'] = floatval($shipment->actual_weight);
//                    $details['pickup_request_id_unpadded'] = $pickup_request_id;
//                    $details['rider_assigned'] = $rider_assigned_flag;
                    $details['city'] = $shipment->consignee_city->name;
                    $details['hub'] = $shipment->consignee_city->hub_city->name;

                    ShipmentScanningJourneyController::add($shipment->id ,32,4,Auth::id(),NULL,NULL,NULL,NULL, NULL, NULL, 1);


                    return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
                } else {
                    return ['status' => 1, 'error' => 'Given Tracking Number\'s is not try and buy'];
                }
            } else {
                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
        }
    }


    public function receive_shipment_index()
    {
        return view('retail.pudo.receive_shipment');
    }
    public function receive_shipment_details(Request $request)
    {
        $shipment = Shipment::where('tracking_number',$request->tracking_number);

        if($shipment->exists()) {

            $shipment = $shipment->first();

            $pudo_deliver = PudoDeliverShipment::where('shipment_id',$shipment->id)->first();
            if(!$pudo_deliver){
                return ['status' => 1, 'error' => 'This shipment is not belong to PUDO Shipment!'];
            }


            //todo: now checking canceled shipment arrival
            $user = ShipmentsJourney::where('shipment_id',$shipment->id)->select('user_id','shipper_status_id')->orderby('id','desc')->first();

            if($user->shipper_status_id == 17)
            {
                $canceled_shipment = CancelledShipmentArrival::where('shipper_id',$user->user_id)->first();
                if($canceled_shipment)
                {
                    return ['status' => 1, 'error' => 'Shipment is not allowed for arrival because shipper cancelled this shipment!'];
                }
            }

            $amount = NULL;

            if(session('role_id') != 1){
                if($pudo_deliver->hub_id) {
                    if($pudo_deliver->hub_id != session('hub_id')){
                        return ['status' => 1, 'error' => 'You can not do receive of this hub\'s shipment'];
                    }
                } else {
                    $shipment_destination = $shipment->consignee_city->hub_id;
                    if($shipment_destination != session('hub_id')){
                        return ['status' => 1, 'error' => 'You can not do receive of this hub\'s shipment'];
                    }
                }
            }

            if ($shipment->shipper_status_id == 153) {
                if($shipment->booking_type_id == 3){
                    $details = array();
                    $shipment_items = ShipmentItem::where('shipment_id', $shipment->id)->pluck('id')->toArray();
                    $shipment_items_count = count($shipment_items);

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['shipment_items'] = $shipment_items;
                    $details['shipment_items_count'] = $shipment_items_count;


                    ShipmentScanningJourneyController::add($shipment->id ,35,4,Auth::id(),NULL,NULL,NULL,NULL, NULL, NULL, 1);

                    return ['status' => 2, 'success' => 'Try and Buy Shipment found!', 'details' => $details];
                } else if($shipment->booking_type_id == 1 && $shipment->pieces > 1){
                    $details = array();
                    $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['pieces_count'] = $shipment->pieces;
                    $details['pieces_tracking_numbers'] = $shipment_pieces;
                    ShipmentScanningJourneyController::add($shipment->id ,35,4,Auth::id(),NULL,NULL,NULL,NULL, NULL, NULL, 1);
                    return ['status' => 3, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                }
                else{
//                    if($shipment->shipper_status_id == 17){
//                        AdminPickupsController::generate($shipment->id);
//                    }
//                    $shipment->save();

                    $rider_name ='-';
                    $transfer_note_shipment = TransferNoteShipment::where('shipment_id',$shipment->id)->whereIn('status_id',[1,4])->first();
                    if($transfer_note_shipment) {
                        $rider = $transfer_note_shipment->transfer_note->rider;
                        if(isset($rider)){
                            $rider_name = $rider->trax_id.'-'.$rider->name;
                        }
                    }
                    $details = array();

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['city'] = $shipment->consignee_city->name;
                    $details['hub'] = $shipment->consignee_city->hub_city->name;
                    $details['shipper'] = $shipment->user->name;
                    $details['amount'] = $shipment->amount;
                    $details['rider'] = $rider_name;
                    //$details['weight'] = floatval($shipment->actual_weight);

                    ShipmentScanningJourneyController::add($shipment->id ,35,4,Auth::id(),NULL,NULL,NULL,NULL, NULL, NULL, 1);
                    return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
                }
            } else {
                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
            }
        }
        else {
            $shipment_item = ShipmentItem::find($request->tracking_number);
            $shipment_id   = $shipment_item->shipment_id ?? null;

            $pudo_deliver = PudoDeliverShipment::where('shipment_id', $shipment_id)->first();

            if (!$pudo_deliver) {
                return ['status' => 1, 'error' => 'This shipment is not belong to PUDO Shipment!'];
            }

            if($shipment_item){

                if($pudo_deliver->hub_id != session('hub_id')){
                    return ['status' => 1, 'error' => 'You can not do receive of this hub\'s shipment'];
                }

                $shipment = Shipment::find($shipment_item->shipment_id);
                if($shipment->booking_type_id == 3) {
                    if ($shipment->shipper_status_id == 153) {
                        $details = array();
                        $shipment_items = ShipmentItem::where('shipment_id', $shipment->id)->pluck('id')->toArray();
                        $shipment_items_count = count($shipment_items);

                        $details['id'] = $shipment->id;
                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['shipment_items'] = $shipment_items;
                        $details['shipment_items_count'] = $shipment_items_count;
                        $details['scanned_shipment_item'] = $shipment_item->id;

                        ShipmentScanningJourneyController::add($shipment->id ,35,4,Auth::id(),NULL,NULL,NULL,NULL, NULL, NULL, 1);
                        return ['status' => 2, 'success' => 'Try and Buy Shipment found!', 'details' => $details];
                    } else {
                        return ['status' => 1, 'error' => 'Given Item ID/Tracking Number\'s Shipment has already been modified'];
                    }
                } else {
                    return ['status' => 1, 'error' => 'Given Tracking Number\'s is not try and buy'];
                }
            }
            else {
                $shipment_pieces = ShipmentPiece::where('tracking_number', $request->tracking_number)->first();
                $shipment_id   = $shipment_pieces->shipment_id ?? null;

                $pudo_deliver = PudoDeliverShipment::where('shipment_id', $shipment_id)->first();

                if (!$pudo_deliver) {
                    return ['status' => 1, 'error' => 'This shipment is not belong to PUDO Shipment!'];
                }

                if($shipment_pieces){
//                    $shipment_pieces = $shipment_pieces->first();
                    $shipment = Shipment::find($shipment_pieces->shipment_id);

                    if($pudo_deliver->hub_id != session('hub_id')){
                        return ['status' => 1, 'error' => 'You can not do receive of this hub\'s shipment'];
                    }

                    if ($shipment->shipper_status_id == 153) {
                        $details = array();
                        $shipment_all_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();
                        $details['id'] = $shipment->id;
                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['pieces'] = $shipment->pieces;
                        $details['pieces_tracking_numbers'] = $shipment_all_pieces;
                        $details['scanned_shipment_piece'] = $shipment_pieces->tracking_number;
                        ShipmentScanningJourneyController::add($shipment->id ,35,4,Auth::id(),NULL,NULL,NULL,NULL, NULL, NULL, 1);
                        return ['status' => 3, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                    } else {
                        return ['status' => 1, 'error' => 'Given Item ID/Tracking Number\'s Shipment has already been modified'];
                    }
                }
            }
        }

        return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
    }

    public function receive_shipment_submit(Request $request)
    {
        $shipment_ids = explode(',', $request->shipment_ids);
        $print_shipment_ids = [];

        foreach ($shipment_ids as $key => $shipment_id) {
            $shipment = Shipment::find($shipment_id);
            if (!$shipment) continue;

            $pudo_deliver = PudoDeliverShipment::where('shipment_id', $shipment_id)->first();
            if (!$pudo_deliver) {
                return redirect()->back()->with('error', 'This shipment does not belong to a PUDO shipment!');
            }

            if ($shipment->shipper_status_id == 153) {
                $transfer_note_shipment = TransferNoteShipment::where('shipment_id', $shipment_id)
                    ->whereIn('status_id', [1, 4])
                    ->first();

                if (!$transfer_note_shipment) {
                    return redirect()->back()->with('error', 'Rider transfer note not found!');
                }

                // update transfer note shipment
                $transfer_note_shipment->status_id = 2;
                $transfer_note_shipment->last_status_updated_by = Auth::id();
                $transfer_note_shipment->last_status_updated_at = now();
                $transfer_note_shipment->last_status_updated_type = 3;
                $transfer_note_shipment->save();

                // update transfer note
                $transfer_note = $transfer_note_shipment->transfer_note;
                if ($transfer_note) {
                    $transfer_note->increment('completed_shipments');
                    if ($transfer_note->shipments_count == $transfer_note->completed_shipments) {
                        $transfer_note->status_id = 2;
                        $transfer_note->last_status_updated_by = Auth::id();
                        $transfer_note->last_status_updated_at = now();
                        $transfer_note->last_status_updated_type = 3;
                        $transfer_note->save();
                    }
                }

                $shipment->shipper_status_id = 154;
                $shipment->consignee_status_id =154;
                $shipment->save();

                ShipmentsJourneyController::add(
                    $shipment_id,
                    154, 154, null, null, null, null,
                    $transfer_note_shipment->transfer_note_id,
                    null, 1, null, null, null, null, null,
                    Auth::id()
                );

                $print_shipment_ids[] = $shipment_id;
            }
        }

        return redirect()->back()->with([
            'success' => 'Arrival Done',
            'print_shipment_ids' => $print_shipment_ids
        ]);
    }


    public function receive_piece_details(Request $request)
    {
        $shipment_id = $request->shipment_id;
        $shipment_piece_id = $request->piece_id;

        $shipment_piece = ShipmentPiece::where('tracking_number', $shipment_piece_id);
        if ($shipment_piece->exists()) {
            $shipment_piece = $shipment_piece->first();
            if ($shipment_piece->shipment_id == $shipment_id) {
                $scanned_shipment_piece = $shipment_piece->tracking_number;
                ShipmentScanningJourneyController::add($shipment_id ,35,4,Auth::id(),NULL,NULL,NULL,NULL, NULL, NULL, 1);
                return ['status' => 0, 'success' => 'Shipment Piece found!', 'scanned_shipment_piece' => $scanned_shipment_piece];
            } else {
                return ['status' => 1, 'error' => 'Given Item ID does not belong here'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Shipment Item with given Item ID is present'];
        }
    }

    public function receive_piece_shipment_details(Request $request)
    {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);
        if ($shipment->exists()) {
            $shipment = $shipment->first();

            $pudo_deliver = PudoDeliverShipment::where('shipment_id',$shipment->id)->first();
            if(!$pudo_deliver){
                return ['status' => 1, 'error' => 'This shipment is not belong to PUDO Shipment!'];
            }

            $settings = GlobalSettings::where('type', 'global_rider_id')->first();

            if ($settings) {
                $global_rider_id = $settings->setting_value;
            } else {
                $global_rider_id = 0;
            }
            if ($shipment->shipper_status_id == 153) {
                if ($shipment->pieces > 1) {

                    $retail_shipment = RetailShipment::where('shipment_id', $shipment->id);
                    if ($retail_shipment->exists()) {
                        $shipment->actual_weight = $shipment->estimated_weight;
                    } else {
                        $weight_flag = true;
                        $project_arrival_include_shippers = ProjectArrivalShipper::where('user_id', $shipment->user_id);
                        if ($project_arrival_include_shippers->exists()) {
                            if ($shipment->actual_weight == null) {
                                $weight_flag = true;
                            } else {
                                $weight_flag = false;
                            }
                        }
                        if ($weight_flag == true) {
                            if ($request->has('weight')) {
                                $shipment->actual_weight = $request->weight;
                            }
                        }
                    }
                    $shipment->save();
                    if($request->has('weight_type')) {
                        $shipment_weight = ShipmentsWeightType::where('shipment_id', $shipment->id);
                        if($shipment_weight->exists()){
                            $shipments_weight_type = $shipment_weight->first();
                        }else{
                            $shipments_weight_type = new ShipmentsWeightType;
                        }
                        $weight_type = WeightType::find($request->weight_type);
                        $shipments_weight_type->shipment_id = $shipment->id;
                        $shipments_weight_type->weight_type = $weight_type->id;
                        $shipments_weight_type->save();
                    }

                    $rider_name ='-';
                    $transfer_note_shipment = TransferNoteShipment::where('shipment_id',$shipment->id)->whereIn('status_id',[1,4])->first();
                    if($transfer_note_shipment) {
                        $rider = $transfer_note_shipment->transfer_note->rider;
                        if(isset($rider)){
                            $rider_name = $rider->trax_id.'-'.$rider->name;
                        }
                    }

                    $details = array();

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['shipper'] = $shipment->user->name;
//                    $details['pickup_request_id'] = str_pad($pickup_request->id, 6, '0', STR_PAD_LEFT);
                    $details['rider'] = $rider;
                    $details['amount'] = $shipment->amount;
                    $details['weight'] = floatval($shipment->actual_weight);
//                    $details['pickup_request_id_unpadded'] = $pickup_request_id;
////                    $details['rider_assigned'] = $rider_assigned_flag;
                    $details['city'] = $shipment->consignee_city->name;
                    $details['hub'] = $shipment->consignee_city->hub_city->name;
                    $details['rider'] = $rider_name;

                    ShipmentScanningJourneyController::add($shipment->id ,32,4,Auth::id(),NULL,NULL,NULL,NULL, NULL, NULL, 1);


                    return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
                } else {
                    return ['status' => 1, 'error' => 'Given Tracking Number\'s is not try and buy'];
                }
            } else {
                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
        }
    }
    public function receive_try_and_buy_shipment_details(Request $request)
    {
        $shipment = Shipment::where('tracking_number', $request->tracking_number);
        if ($shipment->exists()) {
            $shipment = $shipment->first();

            $pudo_pickup = PudoPickupShipment::where('shipment_id',$shipment->id)->first();
            if(!$pudo_pickup){
                return ['status' => 1, 'error' => 'This shipment is not eligible for pickup drop-off!'];
            }

            $settings = GlobalSettings::where('type', 'global_rider_id')->first();

            if ($settings) {
                $global_rider_id = $settings->setting_value;
            } else {
                $global_rider_id = 0;
            }
            if ($shipment->shipper_status_id == 153) {
                $rider_assigned_flag = false;
                if ($shipment->booking_type_id == 3) {
//                    if ($shipment->shipper_status_id == 17) {
//                        AdminPickupsController::generate($shipment->id);
//                    }
//                    $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id);
//                    if ($pickup_request_shipment->exists()) {
//                        $pickup_request_shipment = $pickup_request_shipment->latest('id')->first();
//                        $pickup_request_id = $pickup_request_shipment->pickup_request_id;
//                        $pickup_request = V2PickupRequest::find($pickup_request_id);
//                        if ($pickup_request->current_rider_id == null || $pickup_request->current_rider_id === $global_rider_id) {
//                            //                            $rider_id = $this->generate_trax_pickup($pickup_request_id);
//                            //                            $rider = Rider::find($rider_id)->name;
//                            $rider = '';
//                            $rider_assigned_flag = true;
//                        } else {
//                            $rider = $pickup_request->rider->name;
//                        }
//                    } else {
//                        AdminPickupsController::generate($shipment->id);
//
//                        $pickup_request_shipment = V2PickupRequestShipment::where('shipment_id', $shipment->id);
//                        if ($pickup_request_shipment->exists()) {
//                            $pickup_request_shipment = $pickup_request_shipment->latest('id')->first();
//                            $pickup_request_id = $pickup_request_shipment->pickup_request_id;
//                            $pickup_request = V2PickupRequest::find($pickup_request_id);
//                            if ($pickup_request->current_rider_id == null || $pickup_request->current_rider_id === $global_rider_id) {
//                                //                                $rider_id = $this->generate_trax_pickup($pickup_request_id);
//                                //                                $rider = Rider::find($rider_id)->name;
//                                $rider = '';
//                                $rider_assigned_flag = true;
//                            } else {
//                                $rider = $pickup_request->rider->name;
//                            }
//                        }
//                    }
                    $retail_shipment = RetailShipment::where('shipment_id', $shipment->id);
                    if ($retail_shipment->exists()) {
                        $shipment->actual_weight = $shipment->estimated_weight;
                    } else {
                        $weight_flag = true;
                        $project_arrival_include_shippers = ProjectArrivalShipper::where('user_id', $shipment->user_id);
                        if ($project_arrival_include_shippers->exists()) {
                            if ($shipment->actual_weight == null) {
                                $weight_flag = true;
                            } else {
                                $weight_flag = false;
                            }
                        }
                        if ($weight_flag == true) {
                            if ($request->has('weight')) {
                                $shipment->actual_weight = $request->weight;
                            }
                        }
                    }
                    $shipment->save();
                    if($request->has('weight_type')){
                        $shipment_weight = ShipmentsWeightType::where('shipment_id', $shipment->id);
                        if($shipment_weight->exists()){
                            $shipments_weight_type = $shipment_weight->first();
                        }else{
                            $shipments_weight_type = new ShipmentsWeightType;
                        }
                        $weight_type = WeightType::find($request->weight_type);
                        $shipments_weight_type->shipment_id = $shipment->id;
                        $shipments_weight_type->weight_type = $weight_type->id;
                        $shipments_weight_type->save();
                    }

                    $rider_name ='-';
                    $transfer_note_shipment = TransferNoteShipment::where('shipment_id',$shipment->id)->whereIn('status_id',[1,4])->first();
                    if($transfer_note_shipment) {
                        $rider = $transfer_note_shipment->transfer_note->rider;
                        if(isset($rider)){
                            $rider_name = $rider->trax_id.'-'.$rider->name;
                        }
                    }

                    $details = array();

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['shipper'] = $shipment->user->name;
//                    $details['pickup_request_id'] = str_pad($pickup_request->id, 6, '0', STR_PAD_LEFT);
                    $details['rider'] = $rider_name;
                    $details['weight'] = floatval($shipment->actual_weight);
//                    $details['pickup_request_id_unpadded'] = $pickup_request_id;
                    $details['rider_assigned'] = $rider_assigned_flag;
                    $details['city'] = $shipment->consignee_city->name;
                    $details['hub'] = $shipment->consignee_city->hub_city->name;

                    ShipmentScanningJourneyController::add($shipment->id ,32,4,Auth::id(),NULL,NULL,NULL,NULL, NULL, NULL, 1);

                    return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
                } else {
                    return ['status' => 1, 'error' => 'Given Tracking Number\'s is not try and buy'];
                }
            } else {
                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
            }
        } else {
            return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
        }
    }


//    public static function create_delivery_note(
//        $hub_id,
//        $selected_route_id,
//        $selected_rider_id,
//        $shipment_id,
//        $open_box_id,
//        $order_checkbox = false,
//
//    ) {
//        Shipment::where('id', $shipment_id)->update(['shipper_status_id' => 5, 'consignee_status_id' => 5]);
//
//        $total_cod_amount = Shipment::where('id', $shipment_id)->where(function ($query) {
//            $query->where('booking_type_id', '!=', 4)
//                ->orWhere(function ($sub_query) {
//                    $sub_query->where('booking_type_id', '=', 4)
//                        ->where('charges_mode_id', '=', 2);
//                });
//        })->sum('amount');
//
//        $note = DeliveryNote::create([
//            'hub_id' => $hub_id,
//            'rider_id' => $selected_rider_id,
//            'route_id' => empty($selected_route_id) ? '1837' : $selected_route_id,  //TO-6892
//            'shipments_count' => 1,
//            'admin_id' => 558, //gloabl admin
//            'total_cod_amount' => $total_cod_amount,
//            'password' => NULL,
//            'last_updated_at' => Carbon::now(),
//            'ordering' => $order_checkbox
//        ]);
//        if ($note) {
//            DeliveryNoteShipment::create([
//                'delivery_note_id' => $note->id,
//                'shipment_id' => $shipment,
//                'notification' => $notifications[$pos[0]],
//                'rider_information' => $rider_informations[$pos[0]],
//                'ordering' => $serial
//            ]);
//        }
//
//    }







}
