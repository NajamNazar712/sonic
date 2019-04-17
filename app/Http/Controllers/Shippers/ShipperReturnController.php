<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\BookingType;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\ShippingMode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class ShipperReturnController extends Controller
{
    public function __construct() {
        $this->middleware('auth:web,substitute_users');

        $this->middleware('Permission');
    }
    public function confirmation_pending_index(){
        $shipment_status = ShipmentStatus::select('id','name')->get();
        $shipping_mode = ShippingMode::all();
        $service_type = BookingType::all();
        return view('client.return.confirmation_pending')->with(['shipment_status'=>$shipment_status,'shipping_mode'=>$shipping_mode,'service_type'=>$service_type]);
    }
    public function confirmation_pending_list(Request $request){
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
                    ->where('shipments_journey.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipment_status_reason as ssr','ssr.id','=','shipments_journey.status_reason_id')
            ->select('shipments.id as shId','shipments.tracking_number','shipments.tracking_number as tracking','u.name as shipper','u.phone as shipper_phone1','u.phone2 as shipper_phone2','oc.name as origin','dc.name as destination','shipments.order_id','h.name as hub','shipments.consignee_name','shipments.consignee_phone_number_1','shipments.consignee_phone_number_2','shipments.consignee_address','shipments.amount','sm.mode','bt.booking_type as service_type','ss.name as status','shipments_journey.remarks as remarks','ssr.name as reason','shipments_journey.created_at as status_date','shipments_journey.created_at as last_status_date','sj.created_at as arrival', 'shipments.shipper_status_id as shipper_status_id', 'shipments_journey.shipper_status_id as journey_shipper_status_id', 'dc.pickup as pickup', 'shipments.intercepted as intercepted')
            ->where('shipments.shipper_status_id', 12)
            ->where('shipments.user_id', session('user_id'))
            ->groupBy('shipments.id');


        return Datatables::of($shipments)
            ->editColumn('tracking_number',function ($shipments){
                $route = route('cod.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
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
            ->addColumn('shipment_remarks',function ($shipments){
                $remark = '<input class="form-control form-control-sm" value="'.$shipments->remarks.'" />';
                return $remark;
            })
            ->orderColumn('consignee_phone', 'shipments.consignee_phone_number_1 $1, shipments.consignee_phone_number_2 $1')
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
            ->addColumn("action", function ($result) {
                $confirm_button = '<a href="javascript:void(0);" class="dropdown-item returnMarkStatus" data-action="confirm"><i class="ft-plus-circle primary"></i>Confirm</a>';
                $reattempt_button = '<a href="javascript:void(0);" class="dropdown-item returnReattemptStatus"><i class="ft-plus-circle primary"></i>Re-Attempt Request</a>';
//                $intercept = '<button type="button" class="dropdown-item intercept"><div class="row no-gutters align-items-center"><a href=""><i class="ft-plus-circle"></i></a>  Intercept/Re-Book</div></button>';


                    $dropdown = "
                        <span class='dropdown'>
                            <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                                    aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                            <div class='dropdown-menu open-left arrow'>";
                        $dropdown .= $confirm_button;
                        $dropdown .= $reattempt_button;

//                        if ($result->shipper_status_id == 12 && $result->journey_shipper_status_id != 53 && $result->pickup == 1 && $result->intercepted == 0) {
//                            $dropdown .= $intercept;
//                        }

                    $dropdown .= "
                            </div>
                        </span>
                    ";

                    return $dropdown;

            })
            ->make(true);
    }

    public function return_marked_single_status(Request $request){
            $parcel = Shipment::find($request->shipment_id);
            if($parcel){
                if($parcel->shipper_status_id != 20){
                    if (!$parcel->packaging_material_request) {
                        Shipment::where('id',$request->shipment_id)->update(['shipper_status_id'=>20,'consignee_status_id'=>20]);
                        $shipment_history = ShipmentsJourney::where('shipment_id',$request->shipment_id)->latest()->first();
                        ShipmentsJourneyController::add($request->shipment_id, 20, 20, $shipment_history->status_reason_id, $request->remark, session('user_id'), NULL);


//                NotificationsController::send(15, 0, $request->shipment_id);
//                NotificationsController::send(16, 0, $request->shipment_id);

                        ShipmentChargesController::return($request->shipment_id);

                        AdminFinanceController::add_payment($request->shipment_id, 1);
                    }
                    else {
                        Shipment::where('id',$request->shipment_id)->update(['shipper_status_id'=>17,'consignee_status_id'=>17]);
                        $shipment_history = ShipmentsJourney::where('shipment_id',$request->shipment_id)->latest()->first();
                        ShipmentsJourneyController::add($request->shipment_id, 17, 17, $shipment_history->status_reason_id, NULL, session('user_id'),NULL);


//                NotificationsController::send(15, 0, $request->shipment_id);
//                NotificationsController::send(16, 0, $request->shipment_id);
                    }

                    return ['status'=>1,'success'=>"Shipment successfully marked as Shipment - Return Confirm"];
                }
                return ['status'=>0,'error'=>"Something went wrong, try again later!"];
        }
        return ['status'=>0,'error'=>"Something went wrong, try again later!"];

    }

    public function return_marked_status(Request $request){ //update to status 20 for confirm and 13 for re-attempt

        $shipment_ids = $request->shipment_ids;

            foreach ($shipment_ids as $shipment){
                $parcel = Shipment::find($shipment);
                if($parcel->shipper_status_id != 20){

                    $remark_inp = "remark.$shipment";

                    $remarks = ($request->has($remark_inp) && $request->remark[$parcel->id] != null)? $request->remark[$parcel->id] : null;
                    if (!$parcel->packaging_material_request) {

                        $shipment_history = ShipmentsJourney::where('shipment_id',$shipment)->latest()->first();
                        Shipment::where('id',$shipment)->update(['shipper_status_id'=>20,'consignee_status_id'=>20]);
                        ShipmentsJourneyController::add($shipment, 20, 20, $shipment_history->status_reason_id, $remarks, session('user_id'), NULL);

//                    NotificationsController::send(15, 0, $shipment);
//                    NotificationsController::send(16, 0, $shipment);

                        ShipmentChargesController::return($shipment);

                        AdminFinanceController::add_payment($shipment, 1);
                    }
                    else {
                        $shipment_history = ShipmentsJourney::where('shipment_id',$shipment)->latest()->first();
                        Shipment::where('id',$shipment)->update(['shipper_status_id'=>17,'consignee_status_id'=>17]);
                        ShipmentsJourneyController::add($shipment, 17, 17, $shipment_history->status_reason_id, $remarks, session('user_id'), NULL);

                    }
                }
            }
            return response()->json(['status'=>1,'success'=>"Shipment successfully updated as ( Return Confirm )"]);

    }

    public function return_reattempt_status(Request $request){
        $shipment_ids = $request->shipment_ids;
        $not_updated_shipments  = array();
        $updated_shipments  = array();
        if(!empty($shipment_ids)) {
            foreach ($shipment_ids as $shipment) {
                $parcel = Shipment::find($shipment);
                if (($parcel->shipper_status_id != 52) && ($parcel->shipper_status_id == 12)) {

                    $remark_inp = "remark.$shipment";

                    $remarks = ($request->has($remark_inp) && $request->remark[$parcel->id] != null) ? $request->remark[$parcel->id] : null;

                    Shipment::where('id', $shipment)->update(['shipper_status_id' => 52, 'consignee_status_id' => 52]);
                    ShipmentsJourneyController::add($shipment, 52, 52, NULL, $remarks, session('user_id'), NULL);
                    $updated_shipments[] = $parcel->tracking_number;
                } else {
                    $not_updated_shipments[] = $parcel->tracking_number;
                }


            }
            return response()->json(['status' => 1, 'not_updated_shipments' => $not_updated_shipments, 'updated_shipments' => $updated_shipments, 'success' => "Shipments has been requested for Re-Attempt, Please note that this is subjected to final confirmation by Customer Experience!"]);
        }
    }

    public function return_reattempt_single_status(Request $request){
        $parcel = Shipment::find($request->shipment_id);
        if($parcel){
            if($parcel->shipper_status_id != 52){
                if($parcel->shipper_status_id == 12){
                    Shipment::where('id',$request->shipment_id)->update(['shipper_status_id' => 52,'consignee_status_id' => 52]);
                    ShipmentsJourneyController::add($request->shipment_id, 52, 52, NULL, $request->remark, session('user_id'), NULL);

                    return response()->json(['status'=>1,'success'=>"Shipment has been requested for Re-Attempt, Please note that this is subjected to final confirmation by Customer Experience!"]);
                }
                else{
                    return ['status'=>0,'error'=>"Shipment is already updated for Re-attempt!"];
                }
            }
            return ['status'=>0,'error'=>"Shipment is already updated, Please check tracking!"];

        }
        return ['status'=>0,'error'=>"Something went wrong, try again later!"];

    }
}
