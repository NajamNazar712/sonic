<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
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
    }
    public function return_view(){

        return view('admin.return.index');
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
                $join->on('shipments_journey.created_at','=',
                    DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id order by shipments_journey.created_at desc limit 1)'));
            })
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.created_at','=',
                    DB::raw('(select created_at from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipment_status_reason as ssr','ssr.id','=','shipments_journey.status_reason_id')
            ->select('shipments.id as shId','shipments.tracking_number','u.name as shipper','oc.name as origin','dc.name as destination','shipments.order_id','h.name as hub','shipments.consignee_name','shipments.consignee_phone_number_1 as phone','shipments.consignee_address','shipments.amount','sm.mode','bt.booking_type as service_type','ss.name as status','ssr.name as reason','shipments_journey.remarks as remarks','shipments_journey.created_at as status_date','sj.created_at as arrival')
            ->where('shipments.shipper_status_id',12)
            ->groupBy('shipments.id');

        return Datatables::of($shipments)
            ->editColumn('status_date',function ($shipments){
                if($shipments->status_date) {
                    if (2 - ((new \Carbon\Carbon($shipments->status_date, 'UTC'))->diffInDays()) < 0) {
                        $older = Carbon::parse($shipments->status_date)->format('d/m/Y H:i A');
                        return "<span class='danger font-weight-bold'>$older</span>";
                    } else {
                        return Carbon::parse($shipments->status_date)->format('d/m/Y H:i A');
                    }
                }else{
                    return " - ";
                }
            })
            ->editColumn('arrival',function($shipments){
                if($shipments->arrival){
                    return Carbon::parse($shipments->arrival)->format('d/m/Y H:i A');
                }else{
                    return " - ";
                }
            })
            ->addColumn("action", function ($result) {
                return " <span class='dropdown'>
                                            <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                                                    aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                                            <div class='dropdown-menu open-left arrow'>
                                              <a href='#' class='dropdown-item returnMarkStatus' data-action='confirm'><i class='ft-plus-circle primary'></i> Confirm</a>                                         
                                              <a href='#' class='dropdown-item returnMarkStatus' data-action='reattempt'><i class='ft-plus-circle primary'></i> Re-Attempt</a>                                         
                                            </div></span>";
            })
            ->make(true);
    }
    public function return_marked_status(Request $request){ //update to status 20 for confirm and 13 for re-attempt
        $shipment_ids = $request->shipment_ids;
        $admin = Auth::id();
        if($request->action == 'confirm'){
            foreach ($shipment_ids as $shipment){
                Shipment::where('id',$shipment)->update(['shipper_status_id'=>20,'consignee_status_id'=>20]);
                ShipmentsJourney::create([
                    'shipment_id'=>$shipment,
                    'shipper_status_id'=>20,
                    'consignee_status_id'=>20,
                    'admin_id'=>$admin
                ]);
            }
            return ['status'=>1,'success'=>"Shipment successfully marked as Shipment - Return Confirm"];
        }elseif($request->action == 'reattempt'){
            foreach ($shipment_ids as $shipment){
                Shipment::where('id',$shipment)->update(['shipper_status_id'=>13,'consignee_status_id'=>13]);
                ShipmentsJourney::create([
                    'shipment_id'=>$shipment,
                    'shipper_status_id'=>13,
                    'consignee_status_id'=>13,
                    'admin_id'=>$admin
                ]);
            }
            return ['status'=>1,'success'=>"Shipment successfully marked as Shipment - Re-Attempt"];

        }
    }
    public function return_marked_single_status(Request $request){
        $admin = Auth::id();
        if($request->action == 'confirm'){
            Shipment::where('id',$request->shipment_id)->update(['shipper_status_id'=>20,'consignee_status_id'=>20]);
            ShipmentsJourney::create([
                'shipment_id'=>$request->shipment_id,
                'shipper_status_id'=>20,
                'consignee_status_id'=>20,
                'admin_id'=>$admin
            ]);
            return ['status'=>1,'success'=>"Shipment successfully marked as Shipment - Return Confirm"];
        }elseif($request->action == 'reattempt'){
            Shipment::where('id',$request->shipment_id)->update(['shipper_status_id'=>13,'consignee_status_id'=>13]);
            ShipmentsJourney::create([
                'shipment_id'=>$request->shipment_id,
                'shipper_status_id'=>20,
                'consignee_status_id'=>20,
                'admin_id'=>$admin
            ]);
            return ['status'=>1,'success'=>"Shipment successfully marked as Shipment - Re-Attempt"];
        }
        return ['status'=>0,'error'=>"Something went wrong, try again later!"];

    }
    public function return_confirmed_view(){
        return view('admin.return.confirmed');
    }
    public function return_confirmed_list(Request $request){
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->join('shipping_modes as sm','sm.id','=','shipments.shipping_mode_id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftJoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.created_at','=',
                    DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id order by shipments_journey.created_at desc limit 1)'));
            })
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.created_at','=',
                    DB::raw('(select created_at from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipment_status_reason as ssr','ssr.id','=','shipments_journey.status_reason_id')
            ->select('shipments.id as shipment_id','shipments.id as shId','shipments.tracking_number','u.name as shipper','oc.name as origin','dc.name as destination','shipments.order_id','h.name as hub','shipments.consignee_name','shipments.consignee_phone_number_1 as phone','shipments.consignee_address','shipments.amount','sm.mode','bt.booking_type as service_type','ss.name as status','ssr.name as reason','shipments_journey.remarks as remarks','shipments_journey.created_at as status_date','sj.created_at as arrival')
            ->where('shipments.shipper_status_id',20)
            ->groupBy('shipments.id');

        return Datatables::of($shipments)
            ->editColumn('shipment_id',function ($shipment){
                    return "<a href='#'>{$shipment->shId}</a>";
            })
            ->editColumn('status_date',function ($shipments){
                if($shipments->status_date) {
                    if (2 - ((new \Carbon\Carbon($shipments->status_date, 'UTC'))->diffInDays()) < 0) {
                        $older = Carbon::parse($shipments->status_date)->format('d/m/Y H:i A');
                        return "<span class='danger font-weight-bold'>$older</span>";
                    } else {
                        return Carbon::parse($shipments->status_date)->format('d/m/Y H:i A');
                    }
                }else{
                    return " - ";
                }
            })
            ->editColumn('arrival',function($shipments){
                if($shipments->arrival){
                    return Carbon::parse($shipments->arrival)->format('d/m/Y H:i A');
                }else{
                    return " - ";
                }
            })
            ->make(true);
    }
    public function return_confirmed_search(Request $request){
        $type = $request->select_type;
        if($type == 1){
            $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
                ->join('user_shipping_infos AS usi', function ($join) {
                    $join->on('shipments.pickup_address_id', '=', 'usi.id')
                        ->on('shipments.consignee_city_id', '=', 'usi.city_id');
                })
                ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
                ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
                ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
                ->join('shipping_modes as sm','sm.id','=','shipments.shipping_mode_id')
                ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
                ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
                ->leftJoin('shipments_journey', function ($join) {
                    $join->on('shipments_journey.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id order by shipments_journey.created_at desc limit 1)'));
                })
                ->leftJoin('shipments_journey as sj', function ($join) {
                    $join->on('sj.created_at','=',
                        DB::raw('(select created_at from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
                })
                ->leftJoin('shipment_status_reason as ssr','ssr.id','=','shipments_journey.status_reason_id')
                ->select('shipments.id as shipment_id','shipments.id as shId','shipments.tracking_number','u.name as shipper','oc.name as origin','dc.name as destination','shipments.order_id','h.name as hub','shipments.consignee_name','shipments.consignee_phone_number_1 as phone','shipments.consignee_address','shipments.amount','sm.mode','bt.booking_type as service_type','ss.name as status','ssr.name as reason','shipments_journey.remarks as remarks','shipments_journey.created_at as status_date','sj.created_at as arrival')
                ->where('shipments.shipper_status_id',20)
                ->groupBy('shipments.id')->get();
            return $shipments;
        }elseif($type == 2){
            $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
//                ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
                ->join('user_shipping_infos AS usi', function ($join) {
                    $join->on('shipments.pickup_address_id', '=', 'usi.id')
                        ->on('shipments.consignee_city_id', '!=', 'usi.city_id');
                })
                ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
                ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
                ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
                ->join('shipping_modes as sm','sm.id','=','shipments.shipping_mode_id')
                ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
                ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
                ->leftJoin('shipments_journey', function ($join) {
                    $join->on('shipments_journey.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id order by shipments_journey.created_at desc limit 1)'));
                })
                ->leftJoin('shipments_journey as sj', function ($join) {
                    $join->on('sj.created_at','=',
                        DB::raw('(select created_at from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
                })
                ->leftJoin('shipment_status_reason as ssr','ssr.id','=','shipments_journey.status_reason_id')
                ->select('shipments.id as shipment_id','shipments.id as shId','shipments.tracking_number','u.name as shipper','oc.name as origin','dc.name as destination','shipments.order_id','h.name as hub','shipments.consignee_name','shipments.consignee_phone_number_1 as phone','shipments.consignee_address','shipments.amount','sm.mode','bt.booking_type as service_type','ss.name as status','ssr.name as reason','shipments_journey.remarks as remarks','shipments_journey.created_at as status_date','sj.created_at as arrival')
                ->where('shipments.shipper_status_id',20)
                ->groupBy('shipments.id')->get();
            return $shipments;
        }
    }
}
