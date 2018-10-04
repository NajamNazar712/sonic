<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\Admin\ReturnNoteShipment;
use App\Http\Models\BookingType;
use App\Http\Models\City;
use App\Http\Models\Rider;
use App\Http\Models\Route;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\ShippingMode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class ReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    public function return_view(){
        $shipment_status = ShipmentStatus::select('id','name')->get();
        $shipping_mode = ShippingMode::all();
        $service_type = BookingType::all();
        return view('admin.return.index')->with(['shipment_status'=>$shipment_status,'shipping_mode'=>$shipping_mode,'service_type'=>$service_type]);
    }
    public function return_marked_list(Request $request){ //status 12 shipments
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
            ->select('shipments.id as shId','shipments.tracking_number','shipments.tracking_number as tracking','u.name as shipper','u.phone as shipper_phone1','u.phone2 as shipper_phone2','oc.name as origin','dc.name as destination','shipments.order_id','h.name as hub','shipments.consignee_name','shipments.consignee_phone_number_1 as phone','shipments.consignee_address','shipments.amount','sm.mode','bt.booking_type as service_type','ss.name as status','ssr.name as reason','shipments_journey.remarks as remarks','shipments_journey.created_at as status_date','shipments_journey.created_at as last_status_date','sj.created_at as arrival')
            ->where('shipments.shipper_status_id', 12)
            ->groupBy('shipments.id');

        if (session('role_id') != 1) {
            $shipments = $shipments->whereIn('dc.hub_id', session('hubs'));
        }

        return Datatables::of($shipments)
            ->editColumn('tracking_number',function ($shipments){
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('shipper',function ($shipper){
                return "$shipper->shipper | $shipper->shipper_phone1 | $shipper->shipper_phone2";
            })
            ->editColumn('consignee_name',function ($consignee){
                return "$consignee->consignee_name | $consignee->phone";
            })
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
            })            ->addColumn("action", function ($result) {
                $confirm_button = '<a href="javascript:void(0);" class="dropdown-item returnMarkStatus" data-action="confirm"><i class="ft-plus-circle primary"></i> Confirm</a>';
                $re_attempt_button = '<a href="javascript:void(0);" class="dropdown-item returnMarkStatus" data-action="reattempt"><i class="ft-plus-circle primary"></i> Re-Attempt</a>';

                if (session('role_id') == 1 || count(array_intersect([45, 46], session('permissions'))) !== 0) {
                    $dropdown = "
                        <span class='dropdown'>
                            <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                                    aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                            <div class='dropdown-menu open-left arrow'>";

                    if (session('role_id') == 1 || in_array(45, session('permissions'))) {
                      $dropdown .= $confirm_button;
                    }

                    if (session('role_id') == 1 || in_array(46, session('permissions'))) {
                      $dropdown .= $re_attempt_button;
                    }

                    $dropdown .= "
                            </div>
                        </span>
                    ";

                    return $dropdown;
                }
                else {
                    return '';
                }
            })
            ->make(true);
    }
    public function return_marked_status(Request $request){ //update to status 20 for confirm and 13 for re-attempt
        $shipment_ids = $request->shipment_ids;
        $admin = Auth::id();
        if($request->action == 'confirm'){
            foreach ($shipment_ids as $shipment){
                $parcel = Shipment::find($shipment);

                if (!$parcel->packaging_material_request) {
                    $shipment_history = ShipmentsJourney::where('shipment_id',$shipment)->latest()->first();
                    Shipment::where('id',$shipment)->update(['shipper_status_id'=>20,'consignee_status_id'=>20]);
                    ShipmentsJourneyController::add($shipment, 20, 20, $shipment_history->status_reason_id, $shipment_history->remarks, NULL, Auth::id());

                    NotificationsController::send(15, 0, $shipment);
                    NotificationsController::send(16, 0, $shipment);

                    ShipmentChargesController::return($shipment);

                    AdminFinanceController::add_payment($shipment, 1);
                }
                else {
                    $shipment_history = ShipmentsJourney::where('shipment_id',$shipment)->latest()->first();
                    Shipment::where('id',$shipment)->update(['shipper_status_id'=>17,'consignee_status_id'=>17]);
                    ShipmentsJourneyController::add($shipment, 17, 17, $shipment_history->status_reason_id, $shipment_history->remarks, NULL, Auth::id());

                    NotificationsController::send(15, 0, $shipment);
                    NotificationsController::send(16, 0, $shipment);
                }
            }
            return ['status'=>1,'success'=>"Shipment successfully updated as ( Return Confirm )"];
        }elseif($request->action == 'reattempt'){
            foreach ($shipment_ids as $shipment){
                Shipment::where('id',$shipment)->update(['shipper_status_id'=>13,'consignee_status_id'=>13]);
                ShipmentsJourneyController::add($shipment, 13, 13, NULL, NULL, NULL, Auth::id());

                NotificationsController::send(15, 0, $shipment);
                NotificationsController::send(16, 0, $shipment);
            }
            return ['status'=>1,'success'=>"Shipment successfully updated as ( Re-Attempt )"];

        }
    }
    public function return_marked_single_status(Request $request){
        $admin = Auth::id();
        if($request->action == 'confirm'){
            $parcel = Shipment::find($request->shipment_id);

            if (!$parcel->packaging_material_request) {
                Shipment::where('id',$request->shipment_id)->update(['shipper_status_id'=>20,'consignee_status_id'=>20]);
                $shipment_history = ShipmentsJourney::where('shipment_id',$request->shipment_id)->latest()->first();
                ShipmentsJourneyController::add($request->shipment_id, 20, 20, $shipment_history->status_reason_id, $shipment_history->remarks, NULL, Auth::id());


                NotificationsController::send(15, 0, $request->shipment_id);
                NotificationsController::send(16, 0, $request->shipment_id);

                ShipmentChargesController::return($request->shipment_id);

                AdminFinanceController::add_payment($request->shipment_id, 1);
            }
            else {
                Shipment::where('id',$request->shipment_id)->update(['shipper_status_id'=>17,'consignee_status_id'=>17]);
                $shipment_history = ShipmentsJourney::where('shipment_id',$request->shipment_id)->latest()->first();
                ShipmentsJourneyController::add($request->shipment_id, 17, 17, $shipment_history->status_reason_id, $shipment_history->remarks, NULL, Auth::id());


                NotificationsController::send(15, 0, $request->shipment_id);
                NotificationsController::send(16, 0, $request->shipment_id);
            }

            return ['status'=>1,'success'=>"Shipment successfully marked as Shipment - Return Confirm"];
        }elseif($request->action == 'reattempt'){
            Shipment::where('id',$request->shipment_id)->update(['shipper_status_id'=>13,'consignee_status_id'=>13]);
            ShipmentsJourneyController::add($request->shipment_id, 20, 20, NULL, NULL, NULL, Auth::id());

            NotificationsController::send(15, 0, $request->shipment_id);
            NotificationsController::send(16, 0, $request->shipment_id);

            return ['status'=>1,'success'=>"Shipment successfully marked as Shipment - Re-Attempt"];
        }
        return ['status'=>0,'error'=>"Something went wrong, try again later!"];

    }
    public function return_confirmed_view(){
        $shipment_status = ShipmentStatus::select('id','name')->get();
        $shipping_mode = ShippingMode::all();
        $service_type = BookingType::all();
        return view('admin.return.confirmed')->with(['shipment_status'=>$shipment_status,'shipping_mode'=>$shipping_mode,'service_type'=>$service_type]);
    }
    public function return_confirmed_list(Request $request){
        $status_return = array(20,22,24,27,29,30,33,35,37,44,45,46);
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
                    DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id order by shipments_journey.created_at desc limit 1)'));
            })
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                ->where('sj.created_at','=',
                    DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipment_status_reason as ssr','ssr.id','=','shipments_journey.status_reason_id')
            ->select('shipments.id as shipment_id','shipments.id as shId', 'shipments.shipper_status_id', 'shipments.tracking_number as tracking_number', 'shipments.tracking_number as tracking','u.name as shipper', 'oc.hub_id as origin_hub_id', 'oc.name as origin', 'dc.hub_id as destination_hub_id', 'dc.name as destination','shipments.order_id','h.name as hub','shipments.consignee_name','shipments.consignee_phone_number_1 as phone','shipments.consignee_address','shipments.amount','sm.mode','bt.booking_type as service_type','ss.name as status','ssr.name as reason','shipments_journey.remarks as remarks','shipments_journey.created_at as status_date','shipments_journey.created_at as last_status_date','sj.created_at as arrival')
            ->whereIn('shipments.shipper_status_id',$status_return);

        if (session('role_id') != 1) {
            $shipments = $shipments->whereIn('dc.hub_id', session('hubs'));
        }

        $datatables = Datatables::of($shipments)
            ->addColumn('return_pending_for', function ($shipment) {
                if (in_array($shipment->shipper_status_id, [22, 24, 27, 29, 33, 35, 44, 45, 46])) {
                    return 'Shipper';
                }
                else {
                    if ($shipment->origin_hub_id == $shipment->destination_hub_id) {
                        return 'Shipper';
                    }
                    else {
                        return 'Cargo';
                    }
                }
            })
            ->editColumn('tracking_number',function ($shipments){
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
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
            })            ->filterColumn('return_pending_for', function ($query, $keyword) {
                $keyword = strtolower($keyword);

                if (strpos('shipper', $keyword) !== FALSE) {
                    $query->whereIn('shipments.shipper_status_id', [22, 24, 27, 29, 33, 35, 44, 45, 46])
                    ->orWhereRaw('`oc`.`hub_id` = `dc`.`hub_id`');
                }
                else if (strpos('cargo', $keyword) !== FALSE) {
                    $query->whereIn('shipments.shipper_status_id', [20, 30, 37])
                    ->whereRaw('`oc`.`hub_id` != `dc`.`hub_id`');
                }
                else {
                    $query->whereRaw('false');
                }
            });

           return $datatables->make(true);
    }
  public function return_create_index(){
        $riders = Rider::where('status', 1);

        if (session('role_id') != 1) {
            $riders = $riders->whereHas('city', function ($query) {
                $query->whereIn('hub_id', session('hubs'));
            });
        }

        $riders = $riders->get();

        $routes = Route::where('status', 1);

        if (session('role_id') != 1) {
            $routes = $routes->whereHas('city', function ($query) {
                $query->whereIn('hub_id', session('hubs'));
            });
        }

        $routes = $routes->get();

        return view('admin.return.create')->with(['riders'=>$riders,'routes'=>$routes]);
    }
    public function get_shipment_details(Request $request){
        if($request->tracking != ''){
//            $shipment_not_arrived = array(20,24,27,29,33,35,42,44,45,46);
//            $shipment_arrived = array(22,24,27,29,30,33,35,44,45,46);
            $allowed_statuses = array(20,22,24,27,29,30,33,35,37,42,44,45,46);
            $shipment = Shipment::where('tracking_number', $request->tracking)->whereIn('shipper_status_id',$allowed_statuses);
            $status = '';
            if($shipment->exists()) {
                $shipment = $shipment->first();
                $destination_id = $shipment->pickup_address->city_id;
                $destination_id = City::where('id', $destination_id)->select('hub_id')->first();
                $destination_id = $destination_id->hub_id;//first it was origin now for return its destination
                $origin = $shipment->consignee_city->hub_id;//let's suppose consignee city is origin now
                if(!$request->has('hub_id')){
                    if ($destination_id == $origin && ($shipment->shipper_status_id == 20 || $shipment->shipper_status_id == 24 || $shipment->shipper_status_id == 27 || $shipment->shipper_status_id == 29 || $shipment->shipper_status_id == 30 || $shipment->shipper_status_id == 33 || $shipment->shipper_status_id == 35 || $shipment->shipper_status_id == 37 || $shipment->shipper_status_id == 42 || $shipment->shipper_status_id == 44 || $shipment->shipper_status_id == 45 || $shipment->shipper_status_id == 46)) {
                        $destination_city_id = $shipment->pickup_address->city_id;
                        $destination_city = City::find($destination_city_id);
                        if ($destination_city->id == $destination_city->hub_id) {
                            $destination = $destination_city->name;
                            $hub = $destination_city->id;
                        } else {
                            $hubid = $destination_city->hub_id;
                            $destinationHub = City::find($hubid);
                            $destination = $destinationHub->name;
                            $hub = $destinationHub->id;
                        }
                        $service = $shipment->booking_type->booking_type;
                        $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id);
                        if ($shipment_journey->exists()) {
                            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->select('shipper_status_id', 'remarks')->latest()->first();
//                            $remarks = ($shipment_journey->remarks != '')? $shipment_journey->remarks:' - ';
                            $status_id = ($shipment_journey->shipper_status_id) ? $shipment_journey->shipper_status_id : '';
                            if ($status_id != '') {
                                $status_name = ShipmentStatus::where('id', $status_id)->select('name')->first();
                                $status = $status_name->name;
                            } else {
                                $status = ' - ';
                            }
                        }
                        return response()->json(['status' => 0, 'shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'destination' => $destination, 'hub' => $hub, 'consignee_name' => $shipment->consignee_name, 'phone' => $shipment->consignee_phone_number_1, 'address' => $shipment->consignee_address, 'amount' => $shipment->amount, 'service_type' => $service, 'shipment_status' => $status]);

                    } else
                        if ($destination_id != $origin && ($shipment->shipper_status_id == 22 || $shipment->shipper_status_id == 24 || $shipment->shipper_status_id == 27 || $shipment->shipper_status_id == 29 || $shipment->shipper_status_id == 33 || $shipment->shipper_status_id == 35 || $shipment->shipper_status_id == 37 || $shipment->shipper_status_id == 42 || $shipment->shipper_status_id == 44 || $shipment->shipper_status_id == 45 || $shipment->shipper_status_id == 46)) {
                            $destination_city_id = $shipment->pickup_address->city_id;
                            $destination_city = City::find($destination_city_id);
                            if ($destination_city->id == $destination_city->hub_id) {
                                $destination = $destination_city->name;
                                $hub = $destination_city->id;
                            } else {
                                $hubid = $destination_city->hub_id;
                                $destinationHub = City::find($hubid);
                                $destination = $destinationHub->name;
                                $hub = $destinationHub->id;
                            }

                            $service = $shipment->booking_type->booking_type;
                            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id);
                            if ($shipment_journey->exists()) {
                                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->select('shipper_status_id', 'remarks')->latest()->first();
//                            $remarks = ($shipment_journey->remarks != '')? $shipment_journey->remarks:' - ';
                                $status_id = ($shipment_journey->shipper_status_id) ? $shipment_journey->shipper_status_id : '';
                                if ($status_id != '') {
                                    $status_name = ShipmentStatus::where('id', $status_id)->select('name')->first();
                                    $status = $status_name->name;
                                } else {
                                    $status = ' - ';
                                }
                            }
                            return response()->json(['status' => 0, 'shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'destination' => $destination, 'hub' => $hub, 'consignee_name' => $shipment->consignee_name, 'phone' => $shipment->consignee_phone_number_1, 'address' => $shipment->consignee_address, 'amount' => $shipment->amount, 'service_type' => $service, 'shipment_status' => $status]);

                        } else {
                            return ['status' => 1, 'error' => 'Return Shipment not arrived at origin center yet.'];

                        }
                }else
                if($request->has('hub_id') && ($destination_id == $request->hub_id)){
                if ($destination_id == $origin && ($shipment->shipper_status_id == 20 || $shipment->shipper_status_id == 24 || $shipment->shipper_status_id == 27 || $shipment->shipper_status_id == 29 || $shipment->shipper_status_id == 30 || $shipment->shipper_status_id == 33 || $shipment->shipper_status_id == 35 || $shipment->shipper_status_id == 37 || $shipment->shipper_status_id == 42 || $shipment->shipper_status_id == 44 || $shipment->shipper_status_id == 45 || $shipment->shipper_status_id == 46)) {
                    $destination_city_id = $shipment->pickup_address->city_id;
                    $destination_city = City::find($destination_city_id);
                    if ($destination_city->id == $destination_city->hub_id) {
                        $destination = $destination_city->name;
                        $hub = $destination_city->id;
                    } else {
                        $hubid = $destination_city->hub_id;
                        $destinationHub = City::find($hubid);
                        $destination = $destinationHub->name;
                        $hub = $destinationHub->id;
                    }
                    $service = $shipment->booking_type->booking_type;
                    $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id);
                    if ($shipment_journey->exists()) {
                        $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->select('shipper_status_id', 'remarks')->latest()->first();
//                            $remarks = ($shipment_journey->remarks != '')? $shipment_journey->remarks:' - ';
                        $status_id = ($shipment_journey->shipper_status_id) ? $shipment_journey->shipper_status_id : '';
                        if ($status_id != '') {
                            $status_name = ShipmentStatus::where('id', $status_id)->select('name')->first();
                            $status = $status_name->name;
                        } else {
                            $status = ' - ';
                        }
                    }
                    return response()->json(['status' => 0, 'shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'destination' => $destination, 'hub' => $hub, 'consignee_name' => $shipment->consignee_name, 'phone' => $shipment->consignee_phone_number_1, 'address' => $shipment->consignee_address, 'amount' => $shipment->amount, 'service_type' => $service, 'shipment_status' => $status]);

                } else
                    if ($destination_id != $origin && ($shipment->shipper_status_id == 22 || $shipment->shipper_status_id == 24 || $shipment->shipper_status_id == 27 || $shipment->shipper_status_id == 29 || $shipment->shipper_status_id == 33 || $shipment->shipper_status_id == 35 || $shipment->shipper_status_id == 42 || $shipment->shipper_status_id == 44 || $shipment->shipper_status_id == 45 || $shipment->shipper_status_id == 46)) {
                        $destination_city_id = $shipment->pickup_address->city_id;
                        $destination_city = City::find($destination_city_id);
                        if ($destination_city->id == $destination_city->hub_id) {
                            $destination = $destination_city->name;
                            $hub = $destination_city->id;
                        } else {
                            $hubid = $destination_city->hub_id;
                            $destinationHub = City::find($hubid);
                            $destination = $destinationHub->name;
                            $hub = $destinationHub->id;
                        }

                        $service = $shipment->booking_type->booking_type;
                        $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id);
                        if ($shipment_journey->exists()) {
                            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->select('shipper_status_id', 'remarks')->latest()->first();
//                            $remarks = ($shipment_journey->remarks != '')? $shipment_journey->remarks:' - ';
                            $status_id = ($shipment_journey->shipper_status_id) ? $shipment_journey->shipper_status_id : '';
                            if ($status_id != '') {
                                $status_name = ShipmentStatus::where('id', $status_id)->select('name')->first();
                                $status = $status_name->name;
                            } else {
                                $status = ' - ';
                            }
                        }
                        return response()->json(['status' => 0, 'shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'destination' => $destination, 'hub' => $hub, 'consignee_name' => $shipment->consignee_name, 'phone' => $shipment->consignee_phone_number_1, 'address' => $shipment->consignee_address, 'amount' => $shipment->amount, 'service_type' => $service, 'shipment_status' => $status]);

                    } else {
                        return ['status' => 1, 'error' => 'Return Shipment not arrived at origin center yet.'];

                    }
                }else{
                return ['status' => 1, 'error' => 'Different hub, scan shipments of same hub!.'];
                }
            }else{
                return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present, Check tracking'];
            }


        }
    }
    public function return_create_note(Request $request)
    {
        $trackings = explode(',', $request->shipment_ids);
        $count = count($trackings);
        $rider = $request->rider_id;
        $route = $request->route_id;
        $hub_id = $request->hub_id;
        $admin = Auth::id();
        //$errors_not_arrived = array();

        if(!empty($trackings)) {
            $note = ReturnNote::create(['hub_id' => $hub_id, 'rider_id' => $rider,'route_id'=>$route, 'shipments_count' => $count, 'admin_id' => $admin]);
            if($note) {
                foreach ($trackings as $tracking) {
                    $shipment = Shipment::where('id', $tracking);
//                    $shipment = Shipment::where('tracking_number', $tracking)->first();
//                    return $shipment->get();
                    if ($shipment->exists()) {
                        $shipment = $shipment->first();

                            if ($shipment->booking_type_id == 1) { //attempt failed and arrived at origin center

                                ReturnNoteShipment::create(['return_note_id' => $note->id, 'shipment_id' => $tracking]);
                                $shipment->shipper_status_id = 23;
                                $shipment->consignee_status_id = 23;
                                $shipment->save();
                                ShipmentsJourneyController::add($shipment->id, 23, 23, NULL, NULL, NULL, Auth::id(), $note->id, $rider);


                            } else if ($shipment->booking_type_id == 2) {//attempt failed and arrived at origin center

                                ReturnNoteShipment::create(['return_note_id' => $note->id, 'shipment_id' => $tracking]);
                                $shipment->shipper_status_id = 28;
                                $shipment->consignee_status_id = 28;
                                $shipment->save();
                                ShipmentsJourneyController::add($shipment->id, 28, 28, NULL, NULL, NULL, Auth::id(), $note->id, $rider);


                            } else if ($shipment->booking_type_id == 3) {//attempt failed and arrived at origin center

                                ReturnNoteShipment::create(['return_note_id' => $note->id, 'shipment_id' => $tracking]);
                                $shipment->shipper_status_id = 34;
                                $shipment->consignee_status_id = 34;
                                $shipment->save();
                                ShipmentsJourneyController::add($shipment->id, 34, 34, NULL, NULL, NULL, Auth::id(), $note->id, $rider);


                            }
                        }

                    }
                }
                return redirect()->back()->with(['success' => "Return note has been created with Return Note Number:" . $note->id,'print'=>$note->id]);
            }else{

                return ['error'=>"No shipments scanned"];
            }
    }
    public function return_receive_deliveries_view(){
        return view('admin.return.receive');
    }
    public function return_receive_deliveries_list(Request $request){
        $deliveries = ReturnNote::
        join('cities AS oc', 'return_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'return_notes.rider_id', '=', 'riders.id')
            ->join('admins','admins.id','=','return_notes.admin_id')
            ->select(['return_notes.id as return_note','return_notes.id as return_note_id','oc.name as hub','riders.name as rider','admins.name as assignee','return_notes.created_at','return_notes.shipments_count'])
            ->where('return_notes.status',0);

        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('oc.hub_id', session('hubs'));
        }

        $datatables = Datatables::of($deliveries)
        ->editColumn('return_note', function ($deliveries) {
            return "<a href='javascript:void(0);' class='printreturnnote'><u>" . str_pad($deliveries->return_note, 6, '0', STR_PAD_LEFT) . "</u></a>";
        })
        ->addColumn('return_note_id_padded', function ($deliveries) {
            return str_pad($deliveries->return_note_id, 6, '0', STR_PAD_LEFT);
        })
        ->filterColumn('return_notes.id', function ($query, $keyword) {
            return $query->where('return_notes.id', '=', $keyword);
        })
        ->addColumn("action", function ($result) {
            $statusUpdate = route('admin.return.receive.status',['id'=>$result->return_note]);
            $route = route('admin.return.receive.update',['id'=>$result->return_note]);

            $receive_button = '<a href="' . $statusUpdate . '" class="dropdown-item"><i class="ft-plus-circle primary"></i> Receive</a>';
            $shift_shipment_button = '<a href="' . $route . '" class="dropdown-item returnnoteupdate"><i class="ft-plus-circle primary"></i> Edit Shipment</a>';

            if (session('role_id') == 1 || count(array_intersect([50, 51], session('permissions'))) !== 0) {
                $dropdown = "
                    <span class='dropdown'>
                        <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                                aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                        <div class='dropdown-menu open-left arrow'>";

                if (session('role_id') == 1 || in_array(50, session('permissions'))) {
                  $dropdown .= $receive_button;
                }

                if (($result->created_at->diffInMinutes(Carbon::now()) <= 60) && (session('role_id') == 1 || in_array(51, session('permissions')))) {
                  $dropdown .= $shift_shipment_button;
                }

                $dropdown .= "
                        </div>
                    </span>
                ";

                return $dropdown;
            }
            else {
                return '';
            }
            });

        if ($tracking_number = $request->get('search_tracking')) {
            $datatables->join('return_note_shipments as rns', 'return_notes.id', '=', 'rns.return_note_id')
                ->join('shipments as s', 'rns.shipment_id', '=', 's.id')
                ->where('s.tracking_number', '=', $tracking_number);
        }
        if ($return_note_number = $request->get('return_note_number')) {
            $datatables->where('return_notes.id', '=', $return_note_number);
        }

        return $datatables->make(true);

    }

    public function return_receive_update(Request $request,$id){
        return view('admin.return.receive_update')->with('return_note_id',$id);
    }
    public function return_receive_update_list(Request $request,$id){
        $deliveries = ReturnNote::join('return_note_shipments as dns','dns.return_note_id','=','return_notes.id')
            ->join('shipments','shipments.id','=','dns.shipment_id')
            ->join('cities AS oc', 'shipments.consignee_city_id', '=', 'oc.id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->select(['return_notes.id as return_note','shipments.tracking_number','shipments.id as shId','oc.name as destination','shipments.consignee_name','shipments.consignee_phone_number_1 as phone','shipments.consignee_address as address','bt.booking_type as service_type'])
            ->where('return_notes.id',$id);

        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('return_notes.hub_id', session('hubs'));
        }

        return Datatables::of($deliveries)
            ->addColumn("action", function ($deliveries) {
                return "<a href='javascript:void(0);' class='returnnoterow'>Remove</a>";

            })
            ->make(true);
    }
    public function return_receive_update_remove(Request $request){
//        return $request;
        $shipment = ReturnNoteShipment::where(['return_note_id'=>$request->return_note_id,'shipment_id'=>$request->shipment_id]);
        if($shipment->exists()){
            $shipment = $shipment->first();
            $return_note = $shipment->return_note_id;
            $return = ReturnNote::where('id',$return_note);
            if($return->exists()){
                $parcel = Shipment::where('id',$request->shipment_id);
//                return $parcel->get();
                $parcel = $parcel->first();
                $shipper_status = '';
                if($parcel->shipper_status_id == 23){
                    $shipper_status = 44;
                }else if($parcel->shipper_status_id == 28){
                    $shipper_status = 45;
                }else if($parcel->shipper_status_id == 34){
                    $shipper_status = 46;
                }
                ReturnNoteShipment::where(['return_note_id'=>$return_note,'shipment_id'=>$request->shipment_id])->delete();
                $return = $return->first();
                $count = $return->shipments_count;
                $count = $count-1;
                if($count == 0){
                    ReturnNote::where('id',$return_note)->update(['shipments_count'=>$count,'status'=>2]);
                }else{

                    ReturnNote::where('id',$return_note)->update(['shipments_count'=>$count]);
                }
                Shipment::where('id',$request->shipment_id)->update(['shipper_status_id'=>$shipper_status]);
                ShipmentsJourneyController::add($parcel->id, $shipper_status, NULL, NULL, NULL, NULL, Auth::id(),$request->return_note_id);

                return ['status' => 0, 'success' => 'Return Shipment is successfully removed'];
            }else{
                return ['status' => 1, 'error' => 'Something went wrong'];
            }
        }else{
            return ['status' => 1, 'error' => 'Something went wrong'];
        }
    }
    public function return_receive_status(Request $request,$id){
        $return = ReturnNote::where('id',$id)->select('shipments_count')->first();
        $shipment = Shipment::where('id',30)->first();
        return view('admin.return.receive_status')->with(['return_note_id'=>$id,'shipments_count'=>$return->shipments_count]);
    }
    public function return_receive_status_list(Request $request){
        $deliveries = ReturnNote::join('return_note_shipments as dns','dns.return_note_id','=','return_notes.id')
            ->join('shipments','shipments.id','=','dns.shipment_id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('users','shipments.user_id','=','users.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->select(['return_notes.id as return_note','shipments.tracking_number','shipments.id as shId','oc.name as destination','usi.pickup_address as address','users.name as shipper','bt.booking_type as service_type','shipments.booking_type_id','shipments.shipper_status_id','ss.name as current_status_name'])
            ->where('return_notes.id',$request->id);

        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('return_notes.hub_id', session('hubs'));
        }

        return Datatables::of($deliveries)

            ->addColumn('status', function ($deliveries) {
                $delivered_array = array(25,31,38);
                if(in_array($deliveries->shipper_status_id,$delivered_array)){
                    return $deliveries->current_status_name;
                }else{
                    if($deliveries->booking_type_id == 1){
                        $where = array(24);
                    }else if($deliveries->booking_type_id == 2){
                        $where = array(29);
                    }else if($deliveries->booking_type_id == 3){
                        $where = array(35);
                    }
                    $statuses = ShipmentStatus::whereIn('id',$where)->get();
                    $drops = '';
                    foreach ($statuses as $status){
                        $drops .= '<option value="'.$status->id.'">'.$status->name.'</option>';
                    }
                    $select = '<select class="form-control form-control-sm select2 statusDrop" name="status_drop['.$deliveries->shId.']" ><option></option>'.$drops.'</select>';
                    return $select;
                }

            })
            ->addColumn('reason', function ($deliveries) {
                $delivered_array = array(25,31,38);
                if(in_array($deliveries->shipper_status_id,$delivered_array)) {
                    return '';
                }else{
                    $reason = '<select class="form-control form-control-sm select2 reasonDrop" name="reason_drop['.$deliveries->shId.']" ><option></option></select>';
                    return $reason;
                }

            })
            ->addColumn('remarks', function ($deliveries) {
                $delivered_array = array(25,31,38);
                if(in_array($deliveries->shipper_status_id,$delivered_array)) {
                    return '';
                }else{
                    $reason = '<input class="form-control form-control-sm" name="remarks['.$deliveries->shId.']" placeholder="Enter Remarks">';
                    return $reason;
                }

            })
            ->addColumn('action',function($deliveries){
                $delivered_array = array(25,31,38);
                if(in_array($deliveries->shipper_status_id,$delivered_array)) {
                    return '';
                }else{
                    return " <span class='dropdown'>
                                            <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                                                    aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                                            <div class='dropdown-menu open-left arrow'>
                                              <a href='javascript:void(0);' class='dropdown-item clear'><i class='ft-rotate-cw primary'></i> Clear</a>                                         
                                            </div></span>";
                }

            })
            ->make(true);
    }
    public function receive_return_reason(Request $request){

        $status_id = $request->status;
        $statuses = ShipmentStatus::find($status_id)->reasons()->select('id','name')->orderBy('name')->get();

        if(!$statuses->isEmpty()){
            return response()->json(['status'=>0,'reasons'=>$statuses]);
        }else{
            return ['status'=>1,'error'=>'No reasons are defined for this status!'];
        }

    }
    public function receive_return_status_submit(Request $request){
        $shipments = explode(',',$request->shipment_ids);
        $return_note_id = $request->return_note_id;
        $array_returned = array(25,31,38);
        if($return_note_id != '') {
            foreach ($shipments as $shipment) {
                $reasonId = "reason_drop.$shipment";
                $parcel = Shipment::where('id', $shipment)->first();
                if (!in_array($parcel->shipper_status_id, $array_returned)) {
                if ($request->status_drop[$shipment] == 24 || $request->status_drop[$shipment] == 29 || $request->status_drop[$shipment] == 35) {
                    if($request->status_drop[$shipment] != $parcel->shipper_status_id){
                        ShipmentsJourneyController::add($shipment, $request->status_drop[$shipment], NULL, ($request->has($reasonId) ? $request->reason_drop[$shipment] : null), $request->remarks[$shipment], NULL, Auth::id(), $return_note_id);

                        Shipment::where('id', $shipment)->update(['shipper_status_id' => $request->status_drop[$shipment]]);
                    }
                }
//                else {
//                    ShipmentsJourneyController::add($shipment, $request->status_drop[$shipment], $request->status_drop[$shipment], ($request->has($reasonId) ? $request->reason_drop[$shipment] : null), $request->remarks[$shipment], NULL, Auth::id(), $return_note_id);
//
//                    Shipment::where('id', $shipment)->update(['shipper_status_id' => $request->status_drop[$shipment], 'consignee_status_id' => $request->status_drop[$shipment]]);
//                }

                ReturnNoteShipment::where(['return_note_id' => $return_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);
            }

            }
            $shipment_status = ReturnNoteShipment::where(['return_note_id'=>$return_note_id,'status'=>0])->count();
            if($shipment_status == 0){
                ReturnNote::where('id',$return_note_id)->update(['updated_by'=>Auth::id(),'status'=>1]);
            }

            NotificationsController::send(15, $return_note_id);
            NotificationsController::send(16, $return_note_id);

            return redirect()->back()->with(['success'=>'Return Note Status Has Been Updated']);
        }
    }
    public function return_status_delivered(Request $request){
        if(!empty($request->shipment_ids)){
            foreach ($request->shipment_ids as $shipment){
                $parcel = Shipment::where('id',$shipment)->first();
                if($parcel->booking_type_id == 1){
                    ShipmentsJourneyController::add($shipment, 25, 25, NULL, NULL, NULL, Auth::id(),$request->return_note_id);

                    Shipment::where('id',$shipment)->update(['shipper_status_id'=>25,'consignee_status_id'=>25]);
                    ReturnNoteShipment::where(['return_note_id'=>$request->return_note_id,'shipment_id'=>$shipment])->update(['status'=>1]);

                }else if($parcel->booking_type_id == 2){
                    ShipmentsJourneyController::add($shipment, 31, 31, NULL, NULL, NULL, Auth::id(),$request->return_note_id);

                    Shipment::where('id',$shipment)->update(['shipper_status_id'=>31,'consignee_status_id'=>31]);
                    ReturnNoteShipment::where(['return_note_id'=>$request->return_note_id,'shipment_id'=>$shipment])->update(['status'=>1]);

                }else if($parcel->booking_type_id == 3){
                    ShipmentsJourneyController::add($shipment, 38, 38, NULL, NULL, NULL, Auth::id(),$request->return_note_id);

                    Shipment::where('id',$shipment)->update(['shipper_status_id'=>38,'consignee_status_id'=>38]);
                    ReturnNoteShipment::where(['return_note_id'=>$request->return_note_id,'shipment_id'=>$shipment])->update(['status'=>1]);

                }
            }
            $shipment_status = ReturnNoteShipment::where(['return_note_id'=>$request->return_note_id,'status'=>0])->count();
            if($shipment_status == 0){
                ReturnNote::where('id',$request->return_note_id)->update(['updated_by'=>Auth::id(),'status'=>1]);
            }

            NotificationsController::send(15, $request->return_note_id);
            NotificationsController::send(16, $request->return_note_id);

            return ['status'=>0,'success'=>'Return note shipments status are updated to : Delivered to Shipper'];
        }else{
            return ['status'=>1,'error'=>'No shipments selected'];

        }
    }
    public function rrd_print(Request $request){

        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Return Note</title>

                    <style>
                      @page {
                        size: A4 portrait;
                      }

                      * {
                        -webkit-print-color-adjust: exact !important;
                        color-adjust: exact !important;
                      }

                      body {
                        background: none !important;
                        color: #09262e !important;
                        font-size: 0.9rem !important;
                      }

                      hr {
                        border-top: 1px dashed #000000;
                      }

                      table.table-bordered {
                        page-break-inside: avoid;
                      }

                      table.table-bordered tbody tr td {
                        border: 1px solid #09262e !important;
                      }

                      .color.primary {
                        background: #c8c8c8 !important;
                      }

                      .color.secondary {
                        background: #ebebeb !important;
                      }

                      .border {
                        border: 1px solid #09262e !important;
                      }
                    </style>
                  </head>
                  <body>
                    <div>
      ';
        $return_note = ReturnNote::where('id',$request->id);
        if($return_note->exists()) {
            $total_shipments = 0;
            $shipment_ids = ReturnNoteShipment::where('return_note_id',$request->id)->select('shipment_id')->get();
            $filtered_shipments = Shipment::whereIn('id',$shipment_ids)->get();
            $shipment_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="color primary"><strong>S. No.</strong></td>
                            <td class="color primary"><strong>Tracking No.</strong></td>
                            <td class="color primary"><strong>Client Name & Phone No(s).</strong></td>
                            <td class="color primary"><strong>Contact Person</strong></td>
                            <td class="color primary"><strong>Contact Person Phone</strong></td>
                            <td class="color primary"><strong>Client Address</strong></td>
                            <td class="color primary"><strong>No. of Items</strong></td>
                            <td class="color primary" style="width:200px;"><strong>Sign</strong></td>
                          </tr>
        ';


            foreach ($filtered_shipments as $shipment) {
                $total_shipments++;
                $shipment_details_row_start = '
                          <tr>
                            <td>' . $total_shipments . '</td>
                            <td>' . $shipment->tracking_number . '</td>
                            <td>' . $shipment->user->name . ' | ' . $shipment->user->phone . (($shipment->user->phone2) ? (' / ' . $shipment->user->phone2) : '') . '</td>
                            <td>' . $shipment->pickup_address->poc . '</td>
                            <td>' . $shipment->pickup_address->phone . '</td>
                            <td>' . $shipment->pickup_address->pickup_address . '</td>
                            <td>' . $shipment->items->sum('quantity') . '</td>
                            <td></td>

                          </tr>
            ';

                $shipment_details .= $shipment_details_row_start;
            }
            $shipment_details .= '
                        </tbody>
                      </table>
        ';
            $return_note_details = ReturnNote::where('id',$request->id)->first();
            $rider = Rider::where('id',$return_note_details->rider_id)->first();
            $city_name = $return_note_details->hub->name;
            $rider_name = $rider->name;
            $category = $rider->rider_category->name;
            $route_name = $return_note_details->route->code .' ('.$return_note_details->route->start.' to '.$return_note_details->route->end.')';
            $main_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>
                            <td class="text-center align-middle color primary"><strong>Return Note</strong></td>
                            <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                          </tr>
                         
                          <tr>
                            <td class="color secondary"><strong>Rider Name</strong></td>
                            <td>' . $rider_name . '</td>
                            <td rowspan="7" class="pl-1 pr-1 text-center align-middle">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($request->id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($request->id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Category</strong></td>
                            <td>' . $category . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Route</strong></td>
                            <td>' . $route_name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>City</strong></td>
                            <td>' . $city_name  . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Shipments</strong></td>
                            <td>' . $total_shipments . '</td>
                          </tr>
                        
                        </tbody>
                      </table>
        ';
            $html .= $main_details;
            $html .= $shipment_details;

        }

//        return $shipments;


        $html .= '
                    </div>

                    <script>
                      window.onload = function() {
                        window.print();
                      }
                    </script>
                  </body>
                </html>
      ';

        return $html;


    }

}
