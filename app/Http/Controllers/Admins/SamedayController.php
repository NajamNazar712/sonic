<?php

namespace App\Http\Controllers\Admins;


use App\Http\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class SamedayController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    public function sameday_index(){

        return view('admin.sameday.index');
    }
    public function sameday_list(Request $request){
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            //->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            //->join('shipping_modes as sm','sm.id','=','shipments.shipping_mode_id')
//            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_items as si','si.shipment_id','=','shipments.id')
            ->join('products as p','p.id','=','si.product_type_id')
            ->join('shipping_mode_same_day_timings as sms','sms.id','=','shipments.same_day_timing_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipments_journey as dispatched', function ($join) {
                $join->on('dispatched.shipment_id', '=', 'shipments.id')
                    ->where('dispatched.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 5)'));
            })
            ->leftJoin('shipments_journey as regular_delivery', function ($join) {
                $join->on('regular_delivery.shipment_id', '=', 'shipments.id')
                    ->where('regular_delivery.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 14)'));
            })
            ->leftJoin('shipments_journey as replacement_delivery', function ($join) {
                $join->on('replacement_delivery.shipment_id', '=', 'shipments.id')
                    ->where('replacement_delivery.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 30)'));
            })
            ->leftJoin('shipments_journey as trybuy_delivery', function ($join) {
                $join->on('trybuy_delivery.shipment_id', '=', 'shipments.id')
                    ->where('trybuy_delivery.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 36)'));
            })
            ->leftJoin('shipments_journey as last_update', function ($join) {
                $join->on('last_update.shipment_id', '=', 'shipments.id')
                    ->where('last_update.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->leftjoin('shipment_status as sst','sst.id','=','last_update.shipper_status_id')
            ->leftjoin('admins as updater','updater.id','=','last_update.admin_id')
            ->select('shipments.id as shId','shipments.booking_type_id','shipments.tracking_number','u.name as shipper','oc.name as origin','dc.name as destination','shipments.consignee_name','shipments.consignee_phone_number_1 as consignee_phone','shipments.consignee_address','p.product_name','sms.id as timing_id','sms.timing','sj.created_at as arrival','shipments.created_at as booked_date','shipments.pickup_date','dispatched.created_at as dispatched_time','regular_delivery.created_at as delivered_time','trybuy_delivery.created_at as trybuy_delivered','replacement_delivery.created_at as replacement_delivered','updater.name as updated_by','sst.name as current_status','shipments.shipper_status_id', 'shipments.special_instructions as instructions')

            ->where('shipments.shipping_mode_id',4)
            ->whereNotIn('shipments.shipper_status_id',[39,40,41,42,43,47])
            ->groupBy('shipments.id');

        if (session('role_id') != 1) {
            $shipments = $shipments->whereIn('oc.hub_id', session('hubs'));
        }

        return Datatables::of($shipments)
            ->editColumn('tracking_number',function ($shipments){
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('dispatched_time',function ($shipments){
                if($shipments->dispatched_time) {

                        return Carbon::parse($shipments->dispatched_time)->format('d/m/Y h:i A');

                }else{
                    return " - ";
                }
            })
            ->editColumn('booked_date',function($shipments){
                if($shipments->booked_date){
                    return Carbon::parse($shipments->booked_date)->format('d/m/Y h:i A');
                }else{
                    return " - ";
                }
            })
            ->editColumn('arrival',function($shipments){
                if($shipments->arrival){
                    return Carbon::parse($shipments->arrival)->format('d/m/Y h:i A');
                }else{
                    return " - ";
                }
            })
            ->addColumn('delivered_status',function ($shipments){
                if($shipments->delivered_time != '' || $shipments->replacement_delivered != '' || $shipments->trybuy_delivered != ''){
                    if($shipments->booking_type_id == 1){
                        return Carbon::parse($shipments->delivered_time)->format('d/m/Y h:i A');
                    }else if($shipments->booking_type_id == 2){
                        return Carbon::parse($shipments->replacement_delivered)->format('d/m/Y h:i A');
                    }else if($shipments->booking_type_id == 3){
                        return Carbon::parse($shipments->trybuy_delivered)->format('d/m/Y h:i A');
                    }
                }
                else{
                    return " - ";
                }
            })
            ->addColumn('tat',function ($shipments){
//                Carbon::createFromFormat('Y-m-d H:i:s', $shipments->booked_date)->format('h:m:s A');
                $now = Carbon::now();
               return Carbon::parse($now)->diffForHumans($shipments->booked_date,true);
//                return Carbon::parse($shipments->booked_date)->format('d/m/Y h:i A');
            })
            ->addColumn('remaining_time',function ($shipments){
                $now = Carbon::now();
//                $now = $now->startOfDay();
                $pickup_date = Carbon::parse($shipments->pickup_date)->isPast();


                if(!$pickup_date){
                    if($shipments->timing_id == 1){
                        $final_time = Carbon::parse($shipments->pickup_date)->addHours(6);
                    }else if($shipments->timing_id == 2){
                        $final_time = Carbon::parse($shipments->pickup_date)->addHours(10);
                    }
                    $difference = Carbon::parse($final_time)->diffForHumans($now,true);
                    if($now > $final_time){
                        return " - ";
                    }else{
                        return $difference;
                    }
                }else{
                    if($shipments->timing_id == 1){
                        $final_time = Carbon::parse($shipments->booked_date)->addHours(6);
                    }else if($shipments->timing_id == 2){
                        $final_time = Carbon::parse($shipments->booked_date)->addHours(10);
                    }
                    $difference = Carbon::parse($final_time)->diffForHumans($now,true);
                    if($now > $final_time){
                        return " - ";
                    }else{
                        return "<span class='danger font-weight-bold'>$difference</span>";
                    }
                }
            })
            ->addColumn("action", function ($shipments) {
                if (session('role_id') == 1 || count(array_intersect([3, 4], session('permissions'))) !== 0) {
                    $delivery_statuses = array(2, 4, 6, 7, 8, 9, 13, 15);

                    $dropdown = "
                        <span class='dropdown'>
                            <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                                    aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                            <div class='dropdown-menu open-left arrow'>";

                    if (($shipments->shipper_status_id == 1) && (session('role_id') == 1 || in_array(17, session('permissions')))) {
                        $route = route('admin.pickups.pending.index');
                    }
                    else if ((in_array($shipments->shipper_status_id, $delivery_statuses)) && (session('role_id') == 1 || in_array(35, session('permissions')))) {
                        $route = route('admin.delivery.note.index');

                    }
                    else if (($shipments->shipper_status_id == 5) && (session('role_id') == 1 || in_array(36, session('permissions')))) {
                        $route = route('admin.delivery.receive.index');
                    }

                    if (isset($route)) {
                        $dropdown .= "<a href='{$route}' class='dropdown-item update'><i class='ft-plus-circle primary'></i> Update</a>";
                    }

                    $dropdown .= "<a href='javascript:void(0);' class='dropdown-item view_charges'><i class='ft-eye primary'></i> View Charges</a>";

                    $dropdown .= "<a href='javascript:void(0);' class='dropdown-item airwaybill'><i class='ft-printer primary'></i> Print Invoice</a>";

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
}
