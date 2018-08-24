<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\CargoConsignment;
use App\Http\Models\City;
use App\Http\Models\PickupNote;
use App\Http\Models\Rider;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\Shipper\User;
use App\Http\Models\ShippingMode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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
//        $shippers = User::all(['id','name']);
        $cities = City::all(['id','name']);
        $hubs = City::select(['id','name'])->where('hub',1)->get();
        $statuses = ShipmentStatus::all(['id','name']);
        return view('admin.reports.lead_time_report')->with(['cities'=>$cities,'statuses'=>$statuses,'hubs'=>$hubs]);
    }
    public function lead_time_list(Request $request){
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
            ->leftJoin('shipments_journey as radd', function ($join) {
                $join->on('radd.shipment_id', '=', 'shipments.id')
                    ->where('radd.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 4)'));
            })
            ->leftJoin('shipments_journey as fstatus', function ($join) {
                $join->on('fstatus.shipment_id', '=', 'shipments.id')
                    ->where('fstatus.id','>',
                        DB::raw('(select min(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 5)'));
            })
            ->leftJoin('shipments_journey as dd', function ($join) {
                $join->on('dd.shipment_id', '=', 'shipments.id')
                    ->where('dd.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,16,30,36) )'));
            })
            ->leftJoin('shipments_journey as rc', function ($join) {
                $join->on('rc.shipment_id', '=', 'shipments.id')
                    ->where('rc.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(20,42))'));
            })
            ->leftJoin('shipments_journey as rrad', function ($join) {
                $join->on('rrad.shipment_id', '=', 'shipments.id')
                    ->where('rrad.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 22)'));
            })
            ->leftJoin('shipments_journey as rds', function ($join) {
                $join->on('rds.shipment_id', '=', 'shipments.id')
                    ->where('rds.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(24,25,29,31,35,38))'));
            })
            ->leftJoin('shipments_journey as pd', function ($join) {
                $join->on('pd.shipment_id', '=', 'shipments.id')
                    ->where('pd.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(39,40,41,43))'));
            })
            ->leftJoin('shipments_journey as ret_or_del', function ($join) {
                $join->on('ret_or_del.shipment_id', '=', 'shipments.id')
                    ->where('ret_or_del.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,20,30,36,37,42))'));
            })
            ->leftJoin('shipments_journey as lj', function ($join) {
                $join->on('lj.shipment_id', '=', 'shipments.id')
                    ->where('lj.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id)'));
            })
            ->leftJoin('shipment_status as fs','fs.id','=','fstatus.shipper_status_id')
            ->leftJoin('shipment_status as rdss','rdss.id','=','rds.shipper_status_id')
            ->select('shipments.id as Shipment_id','shipments.tracking_number','ubi.account_no','u.name as shipper','oc.name as origin','dc.name as destination','h.name as hub','ss.name as current_status','sj.created_at as arrival_date','radd.created_at as reached_at_destination','fstatus.created_at as first_status_date','fs.name as first_status','dd.created_at as delivered_date','rc.created_at as return_confirm','rrad.created_at as return_reached_at_destination','rds.created_at as return_delivered_date','rdss.name as return_delivered_status','pd.created_at as payment_done_date','shipments.shipper_status_id','ret_or_del.shipper_status_id as return_check','lj.created_at as latest_journey_date')
            ->orderBy('shipments.id','desc' )
            ->groupBy('shipments.id');
        $lead_time = Datatables::of($shipments)
            ->addColumn('transit_tat',function ($shipments){
                return ($shipments->arrival_date && $shipments->reached_at_destination)? with(new Carbon($shipments->arrival_date, 'UTC'))->diffInDays($shipments->reached_at_destination) :'-';
            })
            ->addColumn('attempt_tat',function ($shipments){
                return ($shipments->arrival_date && $shipments->first_status_date)? with(new Carbon($shipments->arrival_date, 'UTC'))->diffInDays($shipments->first_status_date) :'-';
            })
            ->addColumn('dispatch_tat',function ($shipments){
                return ($shipments->reached_at_destination && $shipments->first_status_date)? with(new Carbon($shipments->reached_at_destination, 'UTC'))->diffInDays($shipments->first_status_date) :'-';
            })
            ->addColumn('return_transit_tat',function ($shipments){
                return ($shipments->return_confirm && $shipments->return_reached_at_destination)? with(new Carbon($shipments->return_confirm, 'UTC'))->diffInDays($shipments->return_reached_at_destination) :'-';
            })
            ->addColumn('return_dispatch_tat',function ($shipments){
                return ($shipments->return_delivered_date && $shipments->return_reached_at_destination)? with(new Carbon($shipments->return_reached_at_destination, 'UTC'))->diffInDays($shipments->return_delivered_date) :'-';
            })
            ->addColumn('return_tat',function ($shipments){
                return ($shipments->return_confirm && $shipments->return_delivered_date)? with(new Carbon($shipments->return_confirm, 'UTC'))->diffInDays($shipments->return_delivered_date) :'-';
            })
            ->addColumn('payment_tat',function ($shipments){

                $return = array(20,42);
                if(in_array($shipments->return_check,$return)){
                    return ($shipments->return_delivered_date && $shipments->payment_done_date)? with(new Carbon($shipments->return_delivered_date, 'UTC'))->diffInDays($shipments->payment_done_date) :'-';
                }else{
                    return ($shipments->delivered_date && $shipments->payment_done_date)? with(new Carbon($shipments->delivered_date, 'UTC'))->diffInDays($shipments->payment_done_date) :'-';
                }
            })
            ->addColumn('total_tat',function ($shipments){
                    return ($shipments->arrival_date && $shipments->latest_journey_date)? with(new Carbon($shipments->arrival_date, 'UTC'))->diffInDays($shipments->latest_journey_date) :'-';

            })
            ->editColumn('arrival_date', function ($shipments) {
                return $shipments->arrival_date ? with(new Carbon($shipments->arrival_date))->format('d/m/Y h:i:s A') : '';
            })
            ->editColumn('reached_at_destination', function ($shipments) {
                return $shipments->reached_at_destination ? with(new Carbon($shipments->reached_at_destination))->format('d/m/Y h:i:s A') : '';
            })
            ->editColumn('first_status_date', function ($shipments) {
                return $shipments->first_status_date ? with(new Carbon($shipments->first_status_date))->format('d/m/Y h:i:s A') : '';
            })
            ->editColumn('delivered_date', function ($shipments) {
                return $shipments->delivered_date ? with(new Carbon($shipments->delivered_date))->format('d/m/Y h:i:s A') : '';
            })
            ->editColumn('delivered_date', function ($shipments) {
                return $shipments->delivered_date ? with(new Carbon($shipments->delivered_date))->format('d/m/Y h:i:s A') : '';
            })
            ->editColumn('return_confirm', function ($shipments) {
                return $shipments->return_confirm ? with(new Carbon($shipments->return_confirm))->format('d/m/Y h:i:s A') : '';
            })
            ->editColumn('return_reached_at_destination', function ($shipments) {
                return $shipments->return_reached_at_destination ? with(new Carbon($shipments->return_reached_at_destination))->format('d/m/Y h:i:s A') : '';
            })
            ->editColumn('return_delivered_date', function ($shipments) {
                return $shipments->return_delivered_date ? with(new Carbon($shipments->return_delivered_date))->format('d/m/Y h:i:s A') : '';
            })
            ->editColumn('payment_done_date', function ($shipments) {
                return $shipments->payment_done_date ? with(new Carbon($shipments->payment_done_date))->format('d/m/Y h:i:s A') : '';
            });

            if($tracking = $request->get('search_tracking_no')){
                $lead_time->where('shipments.tracking_number', '=', $tracking);
            }
            if($origin = $request->get('search_origin')){
                $lead_time->where('oc.id','=',$origin);
            }
            if($destination = $request->get('search_destination')){
                $lead_time->where('dc.id','=',$destination);
            }
            if($hub = $request->get('search_hub')){
                $lead_time->where('h.id','=',$hub);
            }
            if($status = $request->get('search_status')){
                $lead_time->where('ss.id','=',$status);
            }
            if ($request->get('search_from') && $request->get('search_to')) {
                $from = $request->get('search_from');
                $to = $request->get('search_to');
                $lead_time->whereBetween('sj.created_at', [$from,$to]);
            }
            return $lead_time->make(true);
    }
     public function qa_index(Request $request){
            return view('admin.reports.qa_report');
     }
     public function qa_list(Request $request){
        $search_date = $request->search_date;
        $qa_data = array();
        $stations = City::where('hub',1)->select('id','name')->get();
        foreach ($stations as $hub) {
            $qa_data[$hub->name]['cargo_pending'] = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                $query->where('hub_id', '=', $hub->id);
            })
            ->whereHas('consignee_city', function($query) use ($hub) {
                $query->where('hub_id', '!=', $hub->id);
            })->whereHas('shipment_journey', function($query) use ($search_date) {
                $query->where('shipper_status_id', '=', 2)
                ->whereDate('created_at', '<=', $search_date);
            })->count();
            $qa_data[$hub->name]['cargo_resolved'] = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                $query->where('hub_id', '=', $hub->id);
            })
                ->whereHas('consignee_city', function($query) use ($hub) {
                    $query->where('hub_id', '!=', $hub->id);
                })->whereHas('shipment_journey', function($query) use ($search_date) {
                    $query->where('shipper_status_id', '=', 3)
                        ->whereDate('created_at','=', $search_date);
                })->count();
            $qa_data[$hub->name]['cargo_transit_pending'] = CargoConsignment::whereDate('created_at','<=',$search_date)->where('status_id','!=',3)->where('origin_city_id',$hub->id)->count();
            $qa_data[$hub->name]['cargo_transit_resolved'] = CargoConsignment::whereDate('updated_at','=',$search_date)->where('status_id','=',3)->where('origin_city_id',$hub->id)->count();
            $pending_status = array(2, 4, 6, 7, 8, 9, 13, 15); //for pending deliveries
            $not_pending_status = array(1, 3, 5, 10, 11,12,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47); //for pending deliveries
            $qa_data[$hub->name]['deliveries_pending'] = Shipment::whereHas('consignee_city', function($query) use ($hub) {
                $query->where('hub_id', '=', $hub->id);
            })
                ->whereDoesntHave('shipment_journey', function($query) use ($search_date,$not_pending_status) {
                    $query->whereDate('created_at', '<=', $search_date)
                        ->whereIn('shipper_status_id', $not_pending_status);
                })
                ->count();

            $qa_data[$hub->name]['deliveries_resolved'] = Shipment::whereHas('consignee_city', function($query) use ($hub) {
                $query->where('hub_id', '=', $hub->id);
            })
                ->whereHas('shipment_journey', function($query) use ($search_date) {
                    $query->whereDate('created_at',$search_date)
                        ->where('shipper_status_id', 5);
                })
                ->count();
            $qa_data[$hub->name]['receive_deliveries_pending'] = DeliveryNote::whereDate('created_at','<=',$search_date)->where('status',0)->count();
            $qa_data[$hub->name]['receive_deliveries_resolved'] = DeliveryNote::whereDate('created_at',$search_date)->where('status',1)->count();
            $qa_data[$hub->name]['return_marked_pending'] = Shipment::whereHas('consignee_city', function($query) use ($hub) {
                $query->where('hub_id', '=', $hub->id);
            })
                ->whereHas('shipment_journey',function ($query) use ($search_date){
                $query->whereDate('created_at','<=',$search_date)
                    ->where('shipper_status_id', 12);
            })->count();
            $qa_data[$hub->name]['return_marked_resolved'] = Shipment::whereHas('consignee_city', function($query) use ($hub) {
                $query->where('hub_id', '=', $hub->id);
            })
            ->whereHas('shipment_journey',function ($query) use ($search_date){
                $query->whereDate('created_at',$search_date)
                    ->whereIn('shipper_status_id', [13,20]);
            })->count();
            $qa_data[$hub->name]['return_confirmed_pending'] = Shipment::whereHas('consignee_city', function($query) use ($hub) {
                $query->where('hub_id', '=', $hub->id);
            })
                ->whereHas('shipment_journey',function ($query) use ($search_date){
                    $query->whereDate('created_at','<=',$search_date)
                        ->where('shipper_status_id', 20);
                })->count();
            $qa_data[$hub->name]['return_confirmed_resolved'] = Shipment::whereHas('consignee_city', function($query) use ($hub) {
                $query->where('hub_id', '=', $hub->id);
            })
                ->whereHas('shipment_journey',function ($query) use ($search_date){
                    $query->whereDate('created_at',$search_date)
                        ->whereIn('shipper_status_id', [21,23]);
                })->count();
            $qa_data[$hub->name]['return_cargo_pending'] = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                $query->where('hub_id', '=', $hub->id);
            })
                ->whereHas('shipment_journey',function ($query) use ($search_date){
                    $query->whereDate('created_at','<=',$search_date)
                        ->whereIn('shipper_status_id', [21,26,32]);
                })->count();
            $qa_data[$hub->name]['return_cargo_resolved'] = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                $query->where('hub_id', '=', $hub->id);
            })
                ->whereHas('shipment_journey',function ($query) use ($search_date){
                    $query->whereDate('created_at',$search_date)
                        ->whereIn('shipper_status_id', [22,27,33]);
                })->count();
            $qa_data[$hub->name]['return_delivery_pending'] = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                $query->where('hub_id', '=', $hub->id);
            })
                ->whereHas('shipment_journey',function ($query) use ($search_date){
                    $query->whereDate('created_at','<=',$search_date)
                        ->whereIn('shipper_status_id', [22,24,27,29,33,35]);
                })->count();
            $qa_data[$hub->name]['return_delivery_resolved'] = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                $query->where('hub_id', '=', $hub->id);
            })
                ->whereHas('shipment_journey',function ($query) use ($search_date){
                    $query->whereDate('created_at','<=',$search_date)
                        ->whereIn('shipper_status_id', [22,24,27,29,33,35]);
                })->count();
            $qa_data[$hub->name]['return_receive_pending'] = ReturnNote::whereDate('created_at','<=',$search_date)->where('hub_id',$hub->id)->where('status',0)->count();
            $qa_data[$hub->name]['return_receive_resolved'] = ReturnNote::whereDate('updated_at',$search_date)->where('hub_id',$hub->id)->where('status',1)->count();
            }

       return $qa_data;
     }
     public function outstanding_shipments_index(Request $request){
        $hubs = City::where('hub',1)->select('id','name')->get();
        return view('admin.reports.outstanding_shipments_report')->with('hubs',$hubs);
     }
     public function outstanding_shipments_list(Request $request){
         $shipments = DeliveryNoteShipment::join('shipments as s', 'delivery_note_shipments.shipment_id', '=', 's.id')
             ->join('cities as dc', 's.consignee_city_id', '=', 'dc.id')
             ->join('cities as hc', 'dc.hub_id', '=', 'hc.id')
             ->join('users as u', 's.user_id', '=', 'u.id')
             ->join('booking_types as bt', 's.booking_type_id', '=', 'bt.id')
             ->leftjoin('shipments_journey as sj', function($join) {
                 $join->on('sj.shipment_id', '=', 's.id')
                     ->where('sj.created_at', '=', DB::raw('(SELECT MAX(created_at) FROM shipments_journey WHERE shipment_id = s.id)'));
             })
             ->leftjoin('shipments_journey as sjd', function($join) {
                 $join->on('sjd.shipment_id', '=', 's.id')
                     ->where('sjd.created_at', '=', DB::raw('(SELECT MAX(created_at) FROM shipments_journey WHERE shipment_id = s.id AND shipper_status_id IN (14, 16, 30, 36))'));
             })
             ->join('shipment_status as ss', 'sj.shipper_status_id', '=', 'ss.id')
             ->join('delivery_note_station_deposit_notes as dnsdn', 'delivery_note_shipments.delivery_note_id', '=', 'dnsdn.delivery_note_id')
             ->select('s.id', 's.tracking_number', 's.consignee_name as consignee', 's.consignee_address as address', 'dc.name as destination', 'hc.name as hub', 'u.name as shipper', 'bt.booking_type as service_type', 's.amount', 'ss.name as current_status', 'sj.updated_at as status_updated_at', 'sj.remarks', 'delivery_note_shipments.delivery_note_id as dncc', 'dnsdn.station_deposit_note_id as sdn', 'sjd.created_at as delivered_at','delivery_note_shipments.status as recovery_status')
             ->whereIn('delivery_note_shipments.status', [7,8,9]);
         $datatables = Datatables::of($shipments)
             ->editColumn('status_updated_at', function($shipment) {
                 return Carbon::parse($shipment->status_updated_at)->format('d/m/Y H:i A');
             })
             ->addColumn('aging', function($shipment) {
                 $updated_at = Carbon::parse($shipment->status_updated_at)->startOfDay();

                 $now = Carbon::now()->startOfDay();

                 return $updated_at->diffInDays($now) . 'd';
             })
             ->filterColumn('aging', function($query, $keyword) {
                 $search = str_replace('d', '', str_replace(' ', '', $keyword));

                 if (filter_var($search, FILTER_VALIDATE_INT)) {
                     $date = Carbon::now();

                     $date = $date->subDays($search);

                     $query->whereDate('sj.updated_at', '>=', $date->toDateString());
                 }
                 else {
                     $query->whereRaw($search);
                 }
             })
            ->editColumn('recovery_status',function ($shipment){
                if($shipment->recovery_status == 7){
                    return "Outstanding";
                }else if($shipment->recovery_status == 8){
                    return "Resolved";
                }else if($shipment->recovery_status == 9){
                    return "Adjust in Payment";
                }
            });

         if ($hub = $request->get('hub')) {
             $datatables->where('hc.id', '=', $hub);
         }
         if($status = $request->get('shipment_status')){
             if($status == 1){
                 $datatables->where('delivery_note_shipments.status','=',7);
             }else if($status == 2){
                 $datatables->where('delivery_note_shipments.status','=',8);

             }else{
                 $datatables->where('delivery_note_shipments.status','=',9);

             }
         }
         if ($delivery_date_from = $request->get('delivery_date_from')) {
             $datatables->where('sjd.created_at', '>=', $delivery_date_from);
         }

         if ($delivery_date_to = $request->get('delivery_date_to')) {
             $datatables->where('sjd.created_at', '<', Carbon::parse($delivery_date_to)->addDay()->toDateTimeString());
         }

         return $datatables->make(true);
     }

     public function daily_pickup_sales_index(Request $request){
        $cities = City::all('id','name');
        return view('admin.reports.daily_pickup_sales_report')->with(['cities'=>$cities]);
     }
     public function daily_pickup_sales_export_to_excel(Request $request){
        $date = $request->date;
         $hubs = City::where('hub',1)->select('id','name')->get();
         $details = array();

         $details[] = ['S. No.','Origin', 'No. of Parcels Booked'];

         $serial_number_hubs = 1;
         foreach ($hubs as $hub) {
            $booked = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                $query->where('hub_id', '=', $hub->id);
            })->count();
             $row = array();

             $row[] = $serial_number_hubs;
             $row[] = $hub->name;
             $row[] = $booked;

             $details[] = $row;

             $serial_number_hubs++;

         }
         $spreadsheet = new Spreadsheet();
         $spreadsheet->getActiveSheet()->fromArray($details);

         $writer = new Xlsx($spreadsheet);

         header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
         header('Content-Disposition: attachment;filename="daily_pickup_sales_report.xlsx"');
         header('Cache-Control: max-age=0');

//         $writer->save('php://output');
         $writer->save('daily_pickup_sales_report.xlsx');
     }
}
