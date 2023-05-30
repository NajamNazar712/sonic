<?php

namespace App\Http\Controllers\Admins\V2Pickup;

use App\Http\Controllers\Admins\AdminPickupsController;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\ShipmentsPickupJourneyController;
use App\Http\Models\Admin\CancelledShipmentArrival;
use App\Http\Models\ConsolidationShipments;
use App\Http\Models\ReceivingSheetReceived;
use App\Http\Models\Rider;
use App\Http\Models\SelfCollectionShipment;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentItem;
use App\Http\Models\ShipmentPiece;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\V2Pickup\V2PickupNote;
use App\Http\Models\V2Pickup\V2PickupNoteRequest;
use App\Http\Models\V2Pickup\V2PickupReceivedShipment;
use App\Http\Models\V2Pickup\V2PickupRequest;
use App\Http\Models\V2Pickup\V2PickupRequestShipment;
use App\Http\Models\Zone;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class V2AdminArrivalServiceController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin')->except('cancel');

        $this->middleware('Permission');
    }
    public function arrival_service_index(){

        return view('admin.v2_pickups.arrival_service_center.arrival_service');
    }
    public function arrival_service_details(Request $request){

        $shipment = Shipment::where('tracking_number',$request->tracking_number);

        if($shipment->exists()) {

            $shipment = $shipment->first();

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
            //todo: now checking canceled shipment arrival end

            $amount = NULL;
            $shipment_origin = $shipment->pickup_address->city->hub_id;
            if(session('role_id') != 1){
                if(!in_array($shipment_origin, session('hubs'))){
                    return ['status' => 1, 'error' => 'You can not do arrival of this hub\'s shipment'];
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

                    ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                    return ['status' => 2, 'success' => 'Try and Buy Shipment found!', 'details' => $details];
                }else if($shipment->booking_type_id == 1 && $shipment->pieces > 1){
                    $details = array();
                    $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['pieces_count'] = $shipment->pieces;
                    $details['pieces_tracking_numbers'] = $shipment_pieces;
                    ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                    return ['status' => 3, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                }
                else{
                    if($shipment->shipper_status_id == 17){
                        AdminPickupsController::generate($shipment->id);
                    }

                    $shipment->save();

                    $details = array();

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['shipper'] = $shipment->user->name;
                    $details['amount'] = $shipment->amount;
                    //$details['weight'] = floatval($shipment->actual_weight);

                    ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                    return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
                }
            } else {
                return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment has already been modified'];
            }
        }
        else {
            $shipment_item = ShipmentItem::find($request->tracking_number);
            if($shipment_item){
                $shipment = Shipment::find($shipment_item->shipment_id);
                if ($shipment->shipper_status_id == 1) {
                    $details = array();
                    $shipment_items = ShipmentItem::where('shipment_id', $shipment->id)->pluck('id')->toArray();
                    $shipment_items_count = count($shipment_items);

                    $details['id'] = $shipment->id;
                    $details['tracking_number'] = $shipment->tracking_number;
                    $details['shipment_items'] = $shipment_items;
                    $details['shipment_items_count'] = $shipment_items_count;
                    $details['scanned_shipment_item'] = $shipment_item->id;

                    ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
                    return ['status' => 2, 'success' => 'Try and Buy Shipment found!', 'details' => $details];
                } else {
                    return ['status' => 1, 'error' => 'Given Item ID/Tracking Number\'s Shipment has already been modified'];
                }
            }
            else {
                $shipment_pieces = ShipmentPiece::where('tracking_number', $request->tracking_number);
                if($shipment_pieces->exists()){
                    $shipment_pieces = $shipment_pieces->first();
                    $shipment = Shipment::find($shipment_pieces->shipment_id);
                    if ($shipment->shipper_status_id == 1) {
                        $details = array();
                        $shipment_all_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();
                        $details['id'] = $shipment->id;
                        $details['tracking_number'] = $shipment->tracking_number;
                        $details['pieces'] = $shipment->pieces;
                        $details['pieces_tracking_numbers'] = $shipment_all_pieces;
                        $details['scanned_shipment_piece'] = $shipment_pieces->tracking_number;
                        ShipmentScanningJourneyController::add($shipment->id, 1, 1, Auth::id(), null, null);
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
                    //dd($data);
                    $shipment->consignee_status_id = 61;
                    $shipment->save();
                    $reference_2_id = NULL;
                    ShipmentsJourneyController::add($shipment_id, 61, 61, NULL, NULL, NULL, Auth::id(), $reference_1_id, $reference_2_id);

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

}
