<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\CargoConsignment;
use App\Http\Models\City;
use App\Http\Models\PickupNote;
use App\Http\Models\Rider;
use App\Http\Models\Shipment;
use App\Http\Models\Shipper\User;
use App\Http\Models\ShippingMode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class AdminReportsController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');
    }
    public function qsr_index(Request $request){
        $shippers = User::whereIn('status',[3,4])->select('id','name')->get();
        $cities = City::all('id','name');
        $hubs = City::where('hub',1)->select('id','name')->get();
        return view('admin.reports.qsr_report')->with(['shippers'=>$shippers,'cities'=>$cities,'hubs'=>$hubs]);
    }
    public function qsr_list(Request $request){
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->select(['shipments.id as shId','shipments.tracking_number','u.name as shipper','ss.name as history_status','bt.booking_type as service_type','sj.created_at as arrival','oc.name as origin','dc.name as destination','h.name as hub','shipments.amount'])
        ->whereNotIn('shipments.shipper_status_id',[1,14,16,17,36,39,40,41,43,47]);
        $datatable = Datatables::of($shipments)
            ->addColumn('aging',function ($shipments){
                $now = Carbon::now();
                $days = Carbon::now()->diffInDays($shipments->arrival);
                if($days == 0){
                    return "-";
                }else{
                    return $days;
                }
            })
            ->editColumn('arrival', function ($shipments) {
                return $shipments->arrival ? with(new Carbon($shipments->arrival))->format('d/m/Y h:i:s A') : '';
            });
            if ($shipper = $request->get('search_shipper')) {
                $datatable->where('u.id', '=', $shipper);
            }
            if ($origin = $request->get('search_origin')) {
                $datatable->where('oc.id', '=', $origin);
            }
            if ($destination = $request->get('search_destination')) {
                $datatable->where('dc.id', '=', $destination);
            }
            if ($hub = $request->get('search_hub')) {
                $datatable->where('h.id', '=', $hub);
            }
            if ($request->get('search_from') && $request->get('search_to')) {
                $from = $request->get('search_from');
                $to = $request->get('search_to');
                $datatable->whereBetween('sj.created_at', [$from,$to]);
            }

            return $datatable->make(true);
    }
    public function return_note_index(Request $request){
        $riders = Rider::all(['id','name']);
        $admins = Admin::all(['id','name']);
        return view('admin.reports.return_notes_report')->with(['riders'=>$riders,'admins'=>$admins]);
    }
    public function return_note_list(Request $request){
        $return_note = ReturnNote::join('riders','riders.id','=','return_notes.rider_id')
            ->join('admins as cr','cr.id','=','return_notes.admin_id')
            ->leftjoin('admins as up','up.id','=','return_notes.updated_by')
            ->select(['return_notes.id as return_note_id','up.name as updated_by','return_notes.shipments_count as count','return_notes.updated_at as submission_date','riders.name as rider','cr.name as created_by','return_notes.created_at']);
        $return = Datatables::of($return_note)
            ->editColumn('submission_date', function ($return_note) {
                return $return_note->submission_date ? with(new Carbon($return_note->submission_date))->format('d/m/Y h:i:s A') : '';
            })
            ->editColumn('created_at', function ($return_note) {
                return $return_note->created_at ? with(new Carbon($return_note->created_at))->format('d/m/Y h:i:s A') : '';
            });

            if($rn_no = $request->get('search_rn_no')){
                $return->where('return_notes.id','=',$rn_no);
            }
            if($tracking = $request->get('search_tracking')){
                $return->join('return_note_shipments as rns','rns.return_note_id','=','return_notes.id')
                ->join('shipments as s', 'rns.shipment_id', '=', 's.id')
                    ->where('s.tracking_number', '=', $tracking);
            }
            if($rider = $request->get('search_rider')){
                $return->where('riders.id','=',$rider);
            }
            if($created_by = $request->get('search_created_by')){
                $return->where('cr.id','=',$created_by);
            }
            if($submitted_by = $request->get('search_submitted_by')){
                $return->where('up.id','=',$submitted_by);
            }
            if($submission_date = $request->get('search_submission')){
                $return->whereDate('return_notes.updated_at',$submission_date);
            }
            return $return->make(true);
    }
    public function pickup_note_index(Request $request){
        $riders = Rider::all(['id','name']);
        $admins = Admin::all(['id','name']);
        $cities = City::all(['id','name']);
        return view('admin.reports.pickup_notes_report')->with(['riders'=>$riders,'admins'=>$admins,'cities'=>$cities]);
    }
    public function pickup_note_list(Request $request){
        $pickup_note = PickupNote::join('cities','cities.id','=','pickup_notes.city_id')
            ->join('riders','riders.id','=','pickup_notes.rider_id')
            ->join('admins as ab','ab.id','=','pickup_notes.assigned_by_user_id')
            ->leftjoin('admins as up','up.id','=','pickup_notes.updated_by')
            ->select(['pickup_notes.id as pn_id','cities.name as city','pickup_notes.pickups','pickup_notes.bookings as count','riders.name as rider','pickup_notes.created_at as assigned_date','ab.name as assigned_by','pickup_notes.updated_at as completed_date','up.name as completed_by'])
            ->where('pickup_notes.status_id',5);
        $return = Datatables::of($pickup_note)
            ->editColumn('assigned_date', function ($pickup_note) {
                return $pickup_note->assigned_date ? with(new Carbon($pickup_note->assigned_date))->format('d/m/Y h:i:s A') : '';
            })
            ->editColumn('completed_date', function ($pickup_note) {
                return $pickup_note->completed_date ? with(new Carbon($pickup_note->completed_date))->format('d/m/Y h:i:s A') : '';
            });

            if($pn_no = $request->get('search_pn_no')){
                $return->where('pickup_notes.id','=',$pn_no);
            }
            if($assigned_by = $request->get('search_assigned_by')){
                $return->where('ab.id','=',$assigned_by);
            }
            if($rider = $request->get('search_rider')){
                $return->where('riders.id','=',$rider);
            }
            if($city = $request->get('search_city')){
                $return->where('cities.id','=',$city);
            }
            if($submitted_by = $request->get('search_completed_by')){
                $return->where('up.id','=',$submitted_by);
            }
            if($submission_date = $request->get('search_completed_date')){
                $return->whereDate('pickup_notes.updated_at',$submission_date);
            }
        return $return->make(true);
    }
    public function cargo_received_index(Request $request){
        $shippimg_modes = ShippingMode::all();
        $cities = City::all(['id','name']);
        return view('admin.reports.cargo_received_report')->with(['cities'=>$cities,'shippimg_modes'=>$shippimg_modes]);
    }
    public function cargo_received_list(Request $request){
        $cargo_received = CargoConsignment::join('cities as oc','oc.id','=','cargo_consignments.origin_city_id')
            ->join('cities as h','h.id','=','cargo_consignments.hub_id')
            ->join('shipping_modes as sm','sm.id','=','cargo_consignments.shipping_mode_id')
            ->join('admins as si','si.id','=','cargo_consignments.sender_id')
            ->join('admins as ri','ri.id','=','cargo_consignments.receiver_id')
            ->select(['cargo_consignments.id as cargo_id','oc.name as origin','h.name as destination','cargo_consignments.shipments','sm.mode as shipping_mode','cargo_consignments.created_at as transit_at','si.name as transit_by','ri.name as received_by','cargo_consignments.updated_at as received_at','cargo_consignments.received_shipments'])
            ->where('cargo_consignments.status_id',3);
        $cargo = Datatables::of($cargo_received)
//            ->addColumn('short_received',function ($cargo){
//                return $cargo->shipments - $cargo->received_shipments;
//            })
//            ->editColumn('received_shipments',function($cargo){
//                return "<a class='received_shipments'>$cargo->received_shipments</a>";
//            })
            ->editColumn('transit_at', function ($cargo) {
                return $cargo->transit_at ? with(new Carbon($cargo->transit_at))->format('d/m/Y h:i:s A') : '';
            })
            ->editColumn('received_at', function ($cargo) {
                return $cargo->received_at ? with(new Carbon($cargo->received_at))->format('d/m/Y h:i:s A') : '';
            });

            if($cargo_no = $request->get('search_cargo_no')){
                $cargo->where('cargo_consignments.id','=',$cargo_no);
            }
            if($origin = $request->get('search_origin')){
                $cargo->where('oc.id','=',$origin);
            }
            if($destination = $request->get('search_destination')){
                $cargo->where('h.id','=',$destination);
            }
            if($mode = $request->get('search_shippimg_modes')){
                $cargo->where('sm.id','=',$mode);
            }
            if($transit_date = $request->get('search_transit_date')){
                $cargo->whereDate('cargo_consignments.created_at',$transit_date);
            }
            if($received_date = $request->get('search_received_date')){
                $cargo->whereDate('cargo_consignments.updated_at',$received_date);
            }
            return $cargo->make(true);
    }
    public function lead_time_index(Request $request){
        $shipments = Shipment::join('users as u','u.id','=','shipments.user_id')
            ->join('user_bank_infos as ubi','ubi.user_id','=','u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            >leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 4)'));
            })
            ->select('shipments.id as Shipment_id','shipments.tracking_number','ubi.account_no','u.name as shipper','oc.name as origin','dc.name as destination','h.name as hub','ss.name as current_status','sj.created_at as arrival_date')
            ->orderBy('shipments.id','desc' )
            ->get();
        return $shipments;
        return view('admin.reports.lead_time_report');
    }
    public function lead_time_list(Request $request){
//        $shipments = Shipment::join('users as u','u.id','=','shipments.user_id')
//            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
//            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
//            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
//            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
//            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
//            ->leftJoin('shipments_journey as sj', function ($join) {
//                $join->on('sj.shipment_id', '=', 'shipments.id')
//                    ->where('sj.created_at','=',
//                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
//            })

    }
}
