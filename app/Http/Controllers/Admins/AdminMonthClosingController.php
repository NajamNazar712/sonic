<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\CargoConsignment;
use App\Http\Models\CargoConsignmentShipment;
use App\Http\Models\ShipmentsJourney;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\ShippingMode;
use App\Http\Models\BookingType;
use App\Http\Models\Shipment;

use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Auth;


class AdminMonthClosingController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function month_closing_index(){
        $shipment_status = ShipmentStatus::select('id','name')->get();
        $shipping_mode = ShippingMode::all();
        $service_type = BookingType::all();
        return view('admin.month_closing.index')->with(['shipment_status'=>$shipment_status,'shipping_mode'=>$shipping_mode,'service_type'=>$service_type]);;
    }

    public function month_closing_list(Request $request){
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->join('shipping_modes as sm','sm.id','=','shipments.shipping_mode_id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftJoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where('shipments_journey.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipment_status_reason as ssr','ssr.id','=','shipments_journey.status_reason_id')
            ->select('shipments.id as shId','shipments.tracking_number','shipments.tracking_number as tracking','u.name as shipper','u.phone as shipper_phone1','u.phone2 as shipper_phone2','oc.name as origin','dc.name as destination','shipments.order_id','h.name as hub','shipments.consignee_name','shipments.consignee_phone_number_1','shipments.consignee_phone_number_2','shipments.consignee_address','shipments.amount','sm.mode','bt.booking_type as service_type','ss.name as status','ssr.name as reason','shipments_journey.remarks as remarks','shipments_journey.created_at as status_date','shipments_journey.created_at as last_status_date','sj.created_at as arrival')
            ->where('shipments.shipper_status_id', 51)
            ->groupBy('shipments.id');

        if (session('role_id') != 1) {
            $shipments = $shipments->whereIn('dc.hub_id', session('hubs'));
        }

        return Datatables::of($shipments)
            ->editColumn('tracking_number',function ($shipments){
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('consignee_phone',function ($shipper){
                return "$shipper->consignee_phone_number_1 | $shipper->consignee_phone_number_2";
            })
            ->filterColumn('consignee_phone',function ($query,$keyword){
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('shipments.consignee_phone_number_1', 'like', '%'.$keyword.'%')->orWhere('shipments.consignee_phone_number_2', 'like', '%'.$keyword.'%');
                }

                else {
                    $query->whereRaw('false');
                }
            })

            ->orderColumn('consignee_phone', 'shipments.consignee_phone_number_1 $1, shipments.consignee_phone_number_2 $1')
            ->editColumn('status_date',function ($shipments){
                if($shipments->status_date) {
                    if (2 - ((new \Carbon\Carbon($shipments->status_date, 'UTC'))->diffInDays()) < 0) {
                        return "<span class='danger font-weight-bold'>" . $shipments->status_date . "</span>";
                    } else {
                        return $shipments->status_date;
                    }
                }else{
                    return " - ";
                }
            })
            ->editColumn('arrival',function($shipments){
                if($shipments->arrival){
                    return $shipments->arrival;
                }else{
                    return " - ";
                }
            })
            ->filterColumn('status',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('ss.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->make(true);
    }
    public function add_shipment(Request $request){
        $tracking_number = $request->tracking_number;
        $shipment = Shipment::where('tracking_number', $tracking_number);
        $status_not_allowed = array(1, 5, 6, 14, 17, 23, 25, 28, 31, 44, 45, 51);
        $intransit_status_array = array(3, 21, 26, 32);

        if($shipment->exists()){
            $shipment_details = $shipment->first();
            if($shipment_details->shipper_status_id != 51){
                if(!in_array($shipment_details->shipper_status_id, $status_not_allowed)){
                    if(in_array($shipment_details->shipper_status_id, [7, 8, 9, 10, 11, 12, 15, 18, 20, 30])) {
                        $delivery_note_shipment = DeliveryNoteShipment::where('shipment_id', $shipment_details->id);
                        if ($delivery_note_shipment->exists()) {
                            $delivery_note_shipment = $delivery_note_shipment->max('delivery_note_id');
                            $delivery = DeliveryNote::where('id', $delivery_note_shipment)->where('status', 0)->exists();
                            if($delivery){
                                return response()->json(['status' => 0, 'error' => 'Shipment is in an Unverified Delivery Note']);
                            }
                        }
                    }
                    if (in_array($shipment_details->shipper_status_id, $intransit_status_array )) {
                        $cargo_consignment_shipment = CargoConsignmentShipment::where('shipment_id', $shipment_details->id);
                        if ($cargo_consignment_shipment->exists()) {
                            $cargo_consignment_shipment = $cargo_consignment_shipment->max('cargo_consignment_id');

                            $cargo = CargoConsignment::find($cargo_consignment_shipment);
                            $cargo->cargo_consignment_shipments()->where('shipment_id', $shipment_details->id)->delete();
                            if (in_array($cargo->status_id, [1, 2])) {
                                $shipments_count = $cargo->shipments;
                                $shipment_weight = $cargo->shipment_weight;
                                $shipments_count = $shipments_count - 1;
                                $cargo->shipments = $shipments_count;
                                $cargo->shipments_weight = $shipment_weight - $shipment_details->actual_weight;
                                if ($shipments_count == 0) {
                                    $cargo->status_id = 5;
                                }
                                $cargo->save();
                                $shipment_details->shipper_status_id = 51;
                                $shipment_details->consignee_status_id = 51;
                                $shipment_details->save();
                                ShipmentsJourneyController::add($shipment_details->id, 51, 51, NULL, NULL, NULL, Auth::id());
                                return response()->json(['status' => 1, 'success' => 'Shipment is successfully added to Month Closing!']);
                            } else if ($cargo->status_id == 4) {
                                $shipments_count = $cargo->shipments;
                                $shipments_received_count = $cargo->received_shipments;
                                $shipment_weight = $cargo->shipment_weight;
                                $shipments_count = $shipments_count - 1;
                                $cargo->shipments = $shipments_count;
                                $cargo->shipments_weight = $shipment_weight - $shipment_details->actual_weight;
                                if ($shipments_count == 0) {
                                    $cargo->status_id = 5;
                                } else if ($shipments_count == $shipments_received_count) {
                                    $cargo->status_id = 3;
                                }
                                $cargo->save();
                                $shipment_details->shipper_status_id = 51;
                                $shipment_details->consignee_status_id = 51;
                                $shipment_details->save();
                                ShipmentsJourneyController::add($shipment_details->id, 51, 51, NULL, NULL, NULL, Auth::id());
                                return response()->json(['status' => 1, 'success' => 'Shipment is successfully added to Month Closing!']);
                            }
                        }

                    } else {

                        if(in_array($shipment_details->shipper_status_id, [20, 21, 22, 24, 26, 27, 29, 30, 47, 48])){
                            AdminFinanceController::return_confirmed_revert($shipment_details->id);
                        }
                        if($shipment_details->shipper_status_id == 30){
                            AdminFinanceController::done_payments_reverted($shipment_details->id);
                        }

                        $shipment_details->shipper_status_id = 51;
                        $shipment_details->consignee_status_id = 51;
                        $shipment_details->save();
                        ShipmentsJourneyController::add($shipment_details->id, 51, 51, NULL, NULL, NULL, Auth::id());
                        return response()->json(['status' => 1, 'success' => 'Shipment is successfully added to Month Closing!']);
                    }

                }
                else{
                    return response()->json(['status' => 0, 'error' => 'Shipment can not added to Month Closing!']);
                }

            }
            else{
                return response()->json(['status' => 0, 'error' => 'Shipment is already added as Month Closing!']);
            }


        }
        else{
            return response()->json(['status' => 0, 'error' => 'Shipment with this tracking number not found!']);
        }
    }

    public function return_confirm_shipment(Request $request){
        $shipment_ids = $request->shipment_ids;
        $not_updated_shipments = array();
        $untouched = false;
            if(!empty($shipment_ids)){

                foreach ($shipment_ids as $shipment){
                    $parcel = Shipment::find($shipment);
                    if($parcel){
                        if (!$parcel->packaging_material_request) {
                            Shipment::where('id',$shipment)->update(['shipper_status_id'=>20,'consignee_status_id'=>20]);
                            ShipmentsJourneyController::add($shipment, 20, 20, NULL, NULL, NULL, Auth::id());

                            NotificationsController::send(15, 0, $shipment);
                            NotificationsController::send(16, 0, $shipment);

                            ShipmentChargesController::return($shipment);

                            AdminFinanceController::add_payment($shipment, 1);
                        }
                        else {
                            Shipment::where('id',$shipment)->update(['shipper_status_id'=>17,'consignee_status_id'=>17]);
                            ShipmentsJourneyController::add($shipment, 17, 17, NULL, NULL, NULL, Auth::id());

                            NotificationsController::send(15, 0, $shipment);
                            NotificationsController::send(16, 0, $shipment);
                        }
                    }else{
                        $not_updated_shipments[] = $shipment;
                    }
                }
            }

            if(count($not_updated_shipments) > 0){
                $untouched = true;
            }

            return response()->json(['status'=>1,'success'=>"Shipment successfully updated as ( Return Confirm )", 'untouched_shipments' => $not_updated_shipments, 'untouched' => $untouched]);
    }

     public function return_reattempt_shipment(Request $request){
         $shipment_ids = $request->shipment_ids;
         $not_updated_shipments = array();
         $untouched = false;
            if(!empty($shipment_ids)){

                foreach ($shipment_ids as $shipment){
                    $parcel = Shipment::find($shipment);
                    if($parcel){
                        Shipment::where('id',$shipment)->update(['shipper_status_id'=>13,'consignee_status_id'=>13]);
                        ShipmentsJourneyController::add($shipment, 13, 13, NULL, NULL, NULL, Auth::id());

                        NotificationsController::send(15, 0, $shipment);
                        NotificationsController::send(16, 0, $shipment);
                    }
                    else{
                        $not_updated_shipments[] = $shipment;
                    }
                }

            }
         if(count($not_updated_shipments) > 0){
             $untouched = true;
         }

             return response()->json(['status'=>1,'success'=>"Shipment successfully updated as ( Re-Attempt )", 'untouched_shipments' => $not_updated_shipments, 'untouched' => $untouched]);

     }
}
