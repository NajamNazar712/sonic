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

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use PHPExcel_Cell;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Yajra\Datatables\Datatables;

class AdminReportsController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
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
        if (session('role_id') != 1) {
            $shipments = $shipments->whereIn('dc.hub_id', session('hubs'));
        }
//            ->orderBy('shipments.id');
        $datatable = Datatables::of($shipments)
            ->addColumn('aging',function ($shipments){

                $days = Carbon::now()->diffInDays($shipments->arrival);
                if($days == 0){
                    return "-";
                }else{
                    return $days;
                }
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
        if (session('role_id') != 1) {
            $return_note = $return_note->whereIn('return_notes.hub_id', session('hubs'));
        }
        $return = Datatables::of($return_note)
            ->editColumn('return_note_id', function ($return_note) {
                return str_pad($return_note->return_note_id, 6, '0', STR_PAD_LEFT);
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
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $return->whereBetween('return_notes.created_at', [$from,$to]);
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
            ->leftjoin('riders','riders.id','=','pickup_notes.rider_id')
            ->leftjoin('admins as ab','ab.id','=','pickup_notes.assigned_by_user_id')
            ->leftjoin('admins as up','up.id','=','pickup_notes.updated_by')
            ->select(['pickup_notes.id as pn_id','cities.name as city','pickup_notes.pickups','pickup_notes.bookings as count','riders.name as rider','pickup_notes.created_at as assigned_date','ab.name as assigned_by','pickup_notes.updated_at as completed_date','up.name as completed_by'])
            ->where('pickup_notes.status_id',4);
        if (session('role_id') != 1) {
            $pickup_note = $pickup_note->whereIn('cities.hub_id', session('hubs'));
        }
        $pickup_note = Datatables::of($pickup_note)
            ->editColumn('pn_id', function ($pickup_note) {
                return str_pad($pickup_note->pn_id, 6, '0', STR_PAD_LEFT);
            });

        if($pn_no = $request->get('search_pn_no')){
            $pickup_note->where('pickup_notes.id','=',$pn_no);
        }
        if($assigned_by = $request->get('search_assigned_by')){
            $pickup_note->where('ab.id','=',$assigned_by);
        }
        if($rider = $request->get('search_rider')){
            $pickup_note->where('riders.id','=',$rider);
        }
        if($city = $request->get('search_city')){
            $pickup_note->where('cities.id','=',$city);
        }
        if($submitted_by = $request->get('search_completed_by')){
            $pickup_note->where('up.id','=',$submitted_by);
        }
        if($submission_date = $request->get('search_completed_date')){
            $pickup_note->whereDate('pickup_notes.updated_at',$submission_date);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $pickup_note->whereBetween('pickup_notes.created_at', [$from,$to]);
        }
        return $pickup_note->make(true);
    }
    public function cargo_received_index(Request $request){
        $shippimg_modes = ShippingMode::all();
        $cities = City::select('id','name')->where('hub',1)->get();
        return view('admin.reports.cargo_received_report')->with(['cities'=>$cities,'shippimg_modes'=>$shippimg_modes]);
    }
    public function cargo_received_list(Request $request){
        $cargo_received = CargoConsignment::join('cities as oc','oc.id','=','cargo_consignments.origin_hub_id')
            ->join('cities as h','h.id','=','cargo_consignments.destination_hub_id')
            ->join('shipping_modes as sm','sm.id','=','cargo_consignments.shipping_mode_id')
            ->join('admins as si','si.id','=','cargo_consignments.sender_id')
            ->join('admins as ri','ri.id','=','cargo_consignments.receiver_id')
            ->select(['cargo_consignments.id as cargo_id','oc.name as origin','h.name as destination','cargo_consignments.shipments','sm.mode as shipping_mode','cargo_consignments.created_at as transit_at','si.name as transit_by','ri.name as received_by','cargo_consignments.updated_at as received_at','cargo_consignments.received_shipments'])
            ->where('cargo_consignments.status_id',3);
        if (session('role_id') != 1) {
            $cargo_received = $cargo_received->where(function ($query) {
                $query->whereIn('oc.hub_id', session('hubs'))->orWhereIn('h.hub_id', session('hubs'));
            });
        }
        $cargo = Datatables::of($cargo_received)
            ->editColumn('cargo_id', function ($cargo_received) {
                return str_pad($cargo_received->cargo_id, 6, '0', STR_PAD_LEFT);
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
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $cargo->whereBetween('cargo_consignments.created_at', [$from,$to]);
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
//            ->join('user_bank_infos as ubi','ubi.user_id','=','u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=' , 'sps.id')
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
            ->select('shipments.id as Shipment_id','shipments.tracking_number','u.id as account_no','u.name as shipper','oc.name as origin','dc.name as destination','h.name as hub','ss.name as current_status','sj.created_at as arrival_date','radd.created_at as reached_at_destination','fstatus.created_at as first_status_date','fs.name as first_status','dd.created_at as delivered_date','rc.created_at as return_confirm','rrad.created_at as return_reached_at_destination','rds.created_at as return_delivered_date','rdss.name as return_delivered_status','pd.created_at as payment_done_date','shipments.shipper_status_id','ret_or_del.shipper_status_id as return_check','lj.created_at as latest_journey_date','sps.name as payment_status')
            ->groupBy('shipments.id');
        if (session('role_id') != 1) {
            $shipments = $shipments->whereIn('dc.hub_id', session('hubs'));
        }
        $lead_time = Datatables::of($shipments)
            ->editColumn('account_no', function ($shipments) {
                return str_pad($shipments->account_no, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('transit_tat',function ($shipments){
                return ($shipments->arrival_date && $shipments->reached_at_destination)? with(new Carbon($shipments->arrival_date, 'UTC'))->diffInDays($shipments->reached_at_destination) :'-';
            })
            ->addColumn('attempt_tat',function ($shipments){
                return ($shipments->arrival_date && $shipments->first_status_date)? with(new Carbon($shipments->arrival_date, 'UTC'))->diffInDays($shipments->first_status_date) :'-';
            })
            ->addColumn('delivered_tat',function ($shipments){
                return ($shipments->arrival_date && $shipments->delivered_date)? with(new Carbon($shipments->arrival_date, 'UTC'))->diffInDays($shipments->delivered_date) :'-';
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
        $from = $request->search_date_from;
        $to = $request->search_date_to;
        $qa_data = array();
        if (session('role_id') != 1) {
            $stations = City::whereIn('hub_id', session('hubs'))->get();
        }else{
            $stations = City::where('hub',1)->select('id','name')->get();
        }
        foreach ($stations as $hub) {
            $qa_data[$hub->name]['cargo_pending'] = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                $query->where('hub_id', '=', $hub->id);
            })
                ->whereHas('consignee_city', function($query) use ($hub) {
                    $query->where('hub_id', '!=', $hub->id);
                })->whereHas('shipment_journey', function($query) use ($from,$to) {
                    $query->where('shipper_status_id', '=', 2)
                        ->whereBetween('created_at', [$from,$to]);
                })->count();
            $qa_data[$hub->name]['cargo_resolved'] = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                $query->where('hub_id', '=', $hub->id);
            })
                ->whereHas('consignee_city', function($query) use ($hub) {
                    $query->where('hub_id', '!=', $hub->id);
                })->whereHas('shipment_journey', function($query) use ($from,$to) {
                    $query->where('shipper_status_id', '=', 3)
                        ->whereBetween('created_at',[$from,$to]);
                })->count();
            $qa_data[$hub->name]['cargo_transit_pending'] = CargoConsignment::whereBetween('created_at',[$from,$to])->where('status_id','!=',3)->where('origin_hub_id',$hub->id)->count();
            $qa_data[$hub->name]['cargo_transit_resolved'] = CargoConsignment::whereBetween('updated_at',[$from,$to])->where('status_id','=',3)->where('origin_hub_id',$hub->id)->count();
            $pending_status = array(2, 4, 6, 7, 8, 9, 13, 15); //for pending deliveries
            $not_pending_status = array(1, 3, 5, 10, 11,12,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47); //for pending deliveries
            $qa_data[$hub->name]['deliveries_pending'] = Shipment::whereHas('consignee_city', function($query) use ($hub) {
                $query->where('hub_id', '=', $hub->id);
            })
                ->whereDoesntHave('shipment_journey', function($query) use ($from,$to,$not_pending_status) {
                    $query->whereBetween('created_at', [$from,$to])
                        ->whereIn('shipper_status_id', $not_pending_status);
                })
                ->count();

            $qa_data[$hub->name]['deliveries_resolved'] = Shipment::whereHas('consignee_city', function($query) use ($hub) {
                $query->where('hub_id', '=', $hub->id);
            })
                ->whereHas('shipment_journey', function($query) use ($from,$to) {
                    $query->whereBetween('created_at',[$from,$to])
                        ->where('shipper_status_id', 5);
                })
                ->count();
            $qa_data[$hub->name]['receive_deliveries_pending'] = DeliveryNote::whereBetween('created_at',[$from,$to])->where('status',0)->count();
            $qa_data[$hub->name]['receive_deliveries_resolved'] = DeliveryNote::whereBetween('created_at',[$from,$to])->where('status',1)->count();
            $qa_data[$hub->name]['return_marked_pending'] = Shipment::whereHas('consignee_city', function($query) use ($hub) {
                $query->where('hub_id', '=', $hub->id);
            })
                ->whereHas('shipment_journey',function ($query) use ($from,$to){
                    $query->whereBetween('created_at',[$from,$to])
                        ->where('shipper_status_id', 12);
                })->count();
            $qa_data[$hub->name]['return_marked_resolved'] = Shipment::whereHas('consignee_city', function($query) use ($hub) {
                $query->where('hub_id', '=', $hub->id);
            })
                ->whereHas('shipment_journey',function ($query) use ($from,$to){
                    $query->whereBetween('created_at',[$from,$to])
                        ->whereIn('shipper_status_id', [13,20]);
                })->count();
            $qa_data[$hub->name]['return_confirmed_pending'] = Shipment::whereHas('consignee_city', function($query) use ($hub) {
                $query->where('hub_id', '=', $hub->id);
            })
                ->whereHas('shipment_journey',function ($query) use ($from,$to){
                    $query->whereBetween('created_at',[$from,$to])
                        ->where('shipper_status_id', 20);
                })->count();
            $qa_data[$hub->name]['return_confirmed_resolved'] = Shipment::whereHas('consignee_city', function($query) use ($hub) {
                $query->where('hub_id', '=', $hub->id);
            })
                ->whereHas('shipment_journey',function ($query) use ($from,$to){
                    $query->whereBetween('created_at',[$from,$to])
                        ->whereIn('shipper_status_id', [21,23]);
                })->count();
            $qa_data[$hub->name]['return_cargo_pending'] = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                $query->where('hub_id', '=', $hub->id);
            })
                ->whereHas('shipment_journey',function ($query) use ($from,$to){
                    $query->whereBetween('created_at',[$from,$to])
                        ->whereIn('shipper_status_id', [21,26,32]);
                })->count();
            $qa_data[$hub->name]['return_cargo_resolved'] = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                $query->where('hub_id', '=', $hub->id);
            })
                ->whereHas('shipment_journey',function ($query) use ($from,$to){
                    $query->whereBetween('created_at',[$from,$to])
                        ->whereIn('shipper_status_id', [22,27,33]);
                })->count();
            $qa_data[$hub->name]['return_delivery_pending'] = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                $query->where('hub_id', '=', $hub->id);
            })
                ->whereHas('shipment_journey',function ($query) use ($from,$to){
                    $query->whereBetween('created_at',[$from,$to])
                        ->whereIn('shipper_status_id', [22,24,27,29,33,35]);
                })->count();
            $qa_data[$hub->name]['return_delivery_resolved'] = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                $query->where('hub_id', '=', $hub->id);
            })
                ->whereHas('shipment_journey',function ($query) use ($from,$to){
                    $query->whereBetween('created_at',[$from,$to])
                        ->whereIn('shipper_status_id', [22,24,27,29,33,35]);
                })->count();
            $qa_data[$hub->name]['return_receive_pending'] = ReturnNote::whereBetween('created_at',[$from,$to])->where('hub_id',$hub->id)->where('status',0)->count();
            $qa_data[$hub->name]['return_receive_resolved'] = ReturnNote::whereBetween('updated_at',[$from,$to])->where('hub_id',$hub->id)->where('status',1)->count();
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
            ->leftjoin('shipment_payment_status as sps', 's.payment_status_id', '=' , 'sps.id')
            ->leftjoin('shipments_journey as sj', function($join) {
                $join->on('sj.shipment_id', '=', 's.id')
                    ->where('sj.created_at', '=', DB::raw('(SELECT MAX(created_at) FROM shipments_journey WHERE shipment_id = s.id)'));
            })
            ->leftjoin('shipments_journey as sjd', function($join) {
                $join->on('sjd.shipment_id', '=', 's.id')
                    ->where('sjd.created_at', '=', DB::raw('(SELECT MAX(created_at) FROM shipments_journey WHERE shipment_id = s.id AND shipper_status_id IN (14, 16, 30, 36))'));
            })
            ->join('shipment_status as ss', 'sj.shipper_status_id', '=', 'ss.id')
            ->leftjoin('delivery_note_station_deposit_notes as dnsdn', 'delivery_note_shipments.delivery_note_id', '=', 'dnsdn.delivery_note_id')
            ->select('s.id', 's.tracking_number', 's.consignee_name as consignee', 's.consignee_address as address', 'dc.name as destination', 'hc.name as hub', 'u.name as shipper', 'bt.booking_type as service_type', 's.amount', 'ss.name as current_status', 'sj.updated_at as status_updated_at', 'sj.remarks', 'delivery_note_shipments.delivery_note_id as dncc', 'dnsdn.station_deposit_note_id as sdn', 'sjd.created_at as delivered_at','delivery_note_shipments.status as recovery_status','sps.name as payment_status')
            ->whereIn('delivery_note_shipments.status', [4,5,6,7,8]);
        if (session('role_id') != 1) {
            $shipments = $shipments->whereIn('dc.hub_id', session('hubs'));
        }
        $datatables = Datatables::of($shipments)
            ->editColumn('dncc', function ($shipments) {
                if ($shipments->dncc) {
                    return str_pad($shipments->dncc, 6, '0', STR_PAD_LEFT);
                }
                else {
                    return '';
                }
            })
            ->editColumn('sdn', function ($shipments) {
                if ($shipments->sdn) {
                    return str_pad($shipments->sdn, 6, '0', STR_PAD_LEFT);
                }
                else {
                    return '';
                }
            })
            ->editColumn('status_updated_at', function($shipment) {
                return $shipment->status_updated_at;
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
                if(in_array($shipment->recovery_status, [4,5,6])){
                    return "Outstanding";
                }else if($shipment->recovery_status == 7){
                    return "Resolved";
                }else if($shipment->recovery_status == 8){
                    return "Payment Adjusted";
                }
            });

        if ($hub = $request->get('hub')) {
            $datatables->where('hc.id', '=', $hub);
        }
        if($status = $request->get('shipment_status')){
            if($status == 1){
                $datatables->whereIn('delivery_note_shipments.status',[4,5,6]);
            }else if($status == 2){
                $datatables->where('delivery_note_shipments.status','=',7);

            }else {
                $datatables->where('delivery_note_shipments.status','=',8);

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
        if (session('role_id') == 1){
            $cities = City::select('id','name')->where('pickup',1)->get();

        }else{
            $cities = City::select('id','name')->where('pickup',1)->whereIn('hub_id',session('hubs'))->get();
        }
        return view('admin.reports.daily_pickup_sales_report')->with(['cities'=>$cities]);
    }
    public function daily_pickup_sales_export_to_excel(Request $request){
        $date = $request->date;
        $date = Carbon::parse($date)->toDateString();
        $search_city = $request->city;
        $city = array();
        $hubs = array();
        if(session('role_id') == 1){
            if($search_city != null){

                $city = City::where('id',$search_city)->select('hub_id')->first();
                $city['id'] = $city->hub->id;
                $city['name'] = $city->hub->name;
                $hubs[] = $city;
            }else{

                $hubs = City::where('hub',1)->select('id','name')->get();
            }
        }else{
            if($search_city != null){

                $city = City::where('id',$search_city)->select('hub_id')->first();
                $city['id'] = $city->hub->id;
                $city['name'] = $city->hub->name;
                $hubs[] = $city;
            }else{

                $hubs = City::whereIn('hub_id', session('hubs'))->select('id','name')->get();
            }
        }

        $details = array();
        $details_shipper = array();

        $details[] = ['S. No.','Origin '.$date, 'No. of Parcels Booked','No of Parcels Received','Revenue without GST','Collection Amount','Avg/Parcel Revenue','Avg. Amount Collection','% Rev. on Amount Collection'];

        $serial_number_hubs = 1;
        $booked = 0; $received = 0; $revenue_wo_gst = 0; $cod_collection = 0;
        foreach ($hubs as $hub) {
            $booked = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                $query->where('hub_id', '=', $hub->id);
            })->whereDate('created_at',$date)->count();
            $received = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                $query->where('hub_id', '=', $hub->id);
            })->whereHas('shipment_journey', function($query) use ($date) {
                $query->whereDate('created_at',$date)
                    ->where('shipper_status_id', 2);
            })->count();
            $cod_collection = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                $query->where('hub_id', '=', $hub->id);
            })->whereHas('shipment_journey', function($query) use ($date) {
                $query->whereDate('created_at',$date)
                    ->where('shipper_status_id', 2);
            })->sum('amount');
            $revenue_wo_gst = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                $query->where('hub_id', '=', $hub->id);
            })->whereHas('shipment_journey', function($query) use ($date) {
                $query->whereDate('created_at',$date)
                    ->where('shipper_status_id', 2);
            })->sum(DB::raw('IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)'));
            $row = array();
            $avg_revenue = ($received != 0)? $revenue_wo_gst/$received:0;
            $avg_cash_collection = ($received != 0)? $cod_collection/$received:0;
            $rev_on_cash_collection = (($avg_cash_collection != 0)? $avg_revenue/$avg_cash_collection:0)*100;
            $row[] = $serial_number_hubs;
            $row[] = $hub->name;
            $row[] = $booked;
            $row[] = $received;
            $row[] = $revenue_wo_gst;
            $row[] = $cod_collection;
            $row[] = $avg_revenue;
            $row[] = $avg_cash_collection;
            $row[] =   $rev_on_cash_collection;
            $details[] = $row;

            $serial_number_hubs++;

        }

        $details_shipper[] = ['S. No.','DSR '.$date, 'No. of Parcels Booked','No of Parcels Received','Revenue without GST','Collection Amount','Avg/Parcel Revenue','Avg. Amount Collection','% Rev. on Amount Collection'];
        $serial_number_shippers = 1;
        if($search_city != null){
            $shippers = User::where('city_id',$search_city)->where('status',3)->get();

        }else{

            $shippers = User::where('status',3)->get();
        }
        $shipper_booked = 0;
        $shipper_received = 0;
        foreach ($shippers as $shipper){
            if($search_city != null){
                $shipper_booked = Shipment::where('user_id',$shipper->id)
                    ->whereHas('pickup_address.city', function($query) use ($hubs) {
                        $query->where('hub_id', '=', $hubs[0]->id);
                    })->whereDate('created_at',$date)->count();
                $shipper_received = Shipment::where('user_id',$shipper->id)
                    ->whereHas('pickup_address.city', function($query) use ($hubs) {
                        $query->where('hub_id', '=', $hubs[0]->id);
                    })
                    ->whereHas('shipment_journey', function($query) use ($date) {
                        $query->whereDate('created_at', $date)
                            ->where('shipper_status_id', 2);
                    })->count();
                $shipper_rev_wo_gst = Shipment::where('user_id',$shipper->id)
                    ->whereHas('pickup_address.city', function($query) use ($hubs) {
                        $query->where('hub_id', '=', $hubs[0]->id);
                    })
                    ->whereHas('shipment_journey', function($query) use ($date) {
                        $query->whereDate('created_at', $date)
                            ->where('shipper_status_id', 2);
                    })->sum(DB::raw('IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)'));
                $shipper_cod = Shipment::where('user_id',$shipper->id)
                    ->whereHas('pickup_address.city', function($query) use ($hubs) {
                        $query->where('hub_id', '=', $hubs[0]->id);
                    })
                    ->whereHas('shipment_journey', function($query) use ($date) {
                        $query->whereDate('created_at', $date)
                            ->where('shipper_status_id', 2);
                    })->sum('amount');

            }else{
                $shipper_booked = Shipment::where('user_id',$shipper->id)->whereDate('created_at',$date)->count();
                $shipper_received = Shipment::where('user_id',$shipper->id)->whereHas('shipment_journey', function($query) use ($date) {
                    $query->whereDate('created_at', $date)
                        ->where('shipper_status_id', 2);
                })->count();
                $shipper_rev_wo_gst = Shipment::where('user_id',$shipper->id)->whereHas('shipment_journey', function($query) use ($date) {
                    $query->whereDate('created_at', $date)
                        ->where('shipper_status_id', 2);
                })->sum(DB::raw('IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)'));
                $shipper_cod = Shipment::where('user_id',$shipper->id)->whereHas('shipment_journey', function($query) use ($date) {
                    $query->whereDate('created_at', $date)
                        ->where('shipper_status_id', 2);
                })->sum('amount');

            }
            $shipper_avg_revenue = ($shipper_received != 0)? $shipper_rev_wo_gst/$shipper_received:0;
            $shipper_avg_cash_collection = ($shipper_received != 0)? $shipper_cod/$shipper_received:0;
            $shipper_rev_on_cash_collection = (($shipper_avg_cash_collection != 0)? $shipper_avg_revenue/$shipper_avg_cash_collection:0)*100;

            $shipper_row = array();
            $shipper_row[] = $serial_number_shippers;
            $shipper_row[] = $shipper->name;
            $shipper_row[] = $shipper_booked;
            $shipper_row[] = $shipper_received;
            $shipper_row[] = $shipper_rev_wo_gst;
            $shipper_row[] = $shipper_cod;
            $shipper_row[] = $shipper_avg_revenue;
            $shipper_row[] = $shipper_avg_cash_collection;
            $shipper_row[] = $shipper_rev_on_cash_collection;

            $details_shipper[] = $shipper_row;
            $serial_number_shippers++;
        }
//         $all_details = array_merge($details + $shipper_details;
        $spreadsheet = new Spreadsheet();
        $cell_st =[
            'font' =>['bold' => true],
            'alignment' =>['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders'=>['bottom' =>['style'=> \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
        ];
        $sheet = $spreadsheet->getActiveSheet();
//        $sheet->getStyle('A1:I1')->applyFromArray($cell_st);
        $sheet->getDefaultColumnDimension()->setWidth(20);
        $sheet->fromArray($details,NULL,'A1');
//        $sheet->getStyle('A21:I21')->applyFromArray($cell_st);
        $count_hubs = count($hubs);
        $count_hubs += 3;
//         $cellIndexShipper = Coordinate::stringFromColumnIndex($count_hubs+2);
        $shipper_cell = 'A'.$count_hubs;
        $sheet->fromArray($details_shipper,NULL,$shipper_cell);
//         $sheet->insertNewRowBefore(9, 8);
        $sheet->setTitle('Daily Pickup Sales Report');
//         $sheet->setCellValue('A1','S. No.');
//         $sheet->fromArray($shipper_details);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="daily_pickup_sales_report.xlsx"');
        header('Cache-Control: max-age=0');

//         $writer->save('php://output');
        $file_name = "reports/daily_pickup_sales_report".Auth::id()."xlsx";
        $writer->save("$file_name");
        return response()->json(['success'=>1,'file'=>'daily_pickup_sales_report.xlsx']);
    }
    public function daily_pickup_sales_download(Request $request){
        $file_name = "/reports/daily_pickup_sales_report".Auth::id()."xlsx";
        $file = public_path().$file_name;
        $headers = array('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',);
        return Response::download($file, 'daily_pickup_sales_report.xlsx',$headers);
    }

    public function customer_sales_index(Request $request){
        if (session('role_id') == 1){
            $shippers = User::where('status',3)->get();
            $hubs = City::select('id','name')->where('hub',1)->get();
        }else{
            $shippers = User::where('status',3)->whereHas('city', function($query) {
                $query->whereIn('hub_id', session('hubs'));
            })->get();
            $hubs = City::select('id','name')->whereIn('id',session('hubs'))->get();
        }
        return view('admin.reports.customer_sales_report')->with(['hubs'=>$hubs,'shippers'=>$shippers]);
    }
    private function get_months($date1, $date2) {
        $time1  = strtotime($date1);
        $time2  = strtotime($date2);
        $my     = date('mY', $time2);

//        $months = array(date('F', $time1));
        $f      = '';

        while($time1 < $time2) {
            $time1 = strtotime((date('Y-m-d', $time1).' +15days'));
            if(date('F', $time1) != $f) {
                $f = date('F', $time1);
                if(date('mY', $time1) != $my && ($time1 < $time2))
                    $months[] = date('F Y', $time1);
            }
        }

        $months[] = date('F Y', $time2);
        return $months;
    }

    public function customer_sales_export_to_excel(Request $request){
        $hub = $request->city;
        $shipper_filter = $request->shipper;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $months_array = array();
        $months_array = $this->get_months($from_date,$to_date);

        if(session('role_id') == 1){
            if($hub != null){
                $city = City::where('id',$hub)->select('id','name')->get();
            }else{
                $city = City::where('hub',1)->select('id','name')->get();
            }
        }else{
            if($hub != null){
                $city = City::where('id',$hub)->select('id','name')->get();
            }else{
                $city = City::whereIn('id',session('hubs'))->select('id','name')->get();
            }
        }


        $details = array();
        $shippers = array();
//        unset($months_array[0]);

        $details['header'] = ['Origin', 'Client Name' ];
        $details['subheader'] = ['Parcels', 'Weight','Collection Amount','Revenue' ];

        foreach ($months_array as $month){
            $details['months'][] = $month;
        }
        $hubs = array();
        $users = array();
        foreach ($city as $c) {
//            $hubid = $c->id;
            $details['hubs'][$c->id] = $c->name;
            if ($shipper_filter != '') {
                $shippers = User::where('id', $shipper_filter)->whereHas('city', function ($query) use ($c) {
                    $query->where('hub_id', '=', $c->id);
                });
            } else {
                $shippers = User::whereHas('city', function ($query) use ($c) {
                    $query->where('hub_id', '=', $c->id);
                });
            }

            if ($shippers->exists()) {
                foreach ($shippers->get() as $key => $s) {
                    $details['shipper'][$c->id][$s->id] = $s->name;
                    foreach ($months_array as $month) {
                        $thisMonth = Carbon::parse($month)->month;
                        $thisYear = Carbon::parse($month)->year;
                        $details['parcels'][$s->id][$month] = Shipment::where('user_id', $s->id)
                            ->whereHas('shipment_journey', function($query) use ($thisMonth,$thisYear) {
                                $query->whereMonth('created_at', $thisMonth)
                                    ->whereYear('created_at', $thisYear)
                                    ->where('shipper_status_id', 2);
                            })->count();
                        $details['weight'][$s->id][$month] = Shipment::where('user_id', $s->id)->whereHas('shipment_journey', function($query) use ($thisMonth,$thisYear) {
                            $query->whereMonth('created_at', $thisMonth)
                                ->whereYear('created_at', $thisYear)
                                ->where('shipper_status_id', 2);
                        })->sum('actual_weight');
                        $details['amount'][$s->id][$month] = Shipment::where('user_id', $s->id)->whereHas('shipment_journey', function($query) use ($thisMonth,$thisYear) {
                            $query->whereMonth('created_at', $thisMonth)
                                ->whereYear('created_at', $thisYear)
                                ->where('shipper_status_id', 2);
                        })->sum('amount');
                        $details['revenue'][$s->id][$month] = Shipment::where('user_id', $s->id)->whereHas('shipment_journey', function($query) use ($thisMonth,$thisYear) {
                            $query->whereMonth('created_at', $thisMonth)
                                ->whereYear('created_at', $thisYear)
                                ->where('shipper_status_id', 2);
                        })->sum(DB::raw('IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)'));

                    }
                }
            }

        }


        //echo "<pre>";print_r($details);echo "</pre>";die();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell_st =[
            'font' =>['bold' => true],
            'alignment' =>['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders'=>['bottom' =>['style'=> \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
        ];
        $sheet->getStyle('A1:B1')->applyFromArray($cell_st);

        $cellIndexcol1 = 3;
        $cellIndexcol2 = 6;

        foreach ($details['months'] as $key => $name) {
            $cellIndex1 = Coordinate::stringFromColumnIndex($cellIndexcol1);
            $cellIndex2 = Coordinate::stringFromColumnIndex($cellIndexcol2);
            $cellIndex11 = $cellIndex1 . '1';
            $cellIndex12 = $cellIndex2 . '1';
            $sheet->mergeCells("$cellIndex11:$cellIndex12");
            $sheet->getStyle("$cellIndex11:$cellIndex12")->applyFromArray($cell_st);
            $sheet->setCellValue($cellIndex11, $name);
            $sheet->fromArray($details['subheader'], NULL, $cellIndex1 . '2');

            $cellIndexcol1 += 4;
            $cellIndexcol2 += 4;

        }
        $sheet->fromArray($details['header'],NULL,'A1');
        $col = 4;
        $parcelIndex = 3;
        $weightIndex = 4;
        $codIndex = 5;
        $revenueIndex = 6;
        foreach ($details['hubs'] as $key => $h) {

            $sheet->setCellValue('A'.$col,$h);
            $sheet->getStyle('A'.$col)->applyFromArray($cell_st);

            if (isset($details['shipper']) && !empty($details['shipper'])) {
                foreach ($details['shipper'] as $hkey => $client){
                    foreach ($client as $ship_key => $cli){
                        if($hkey == $key){
                            $sheet->setCellValue('B'.$col,$cli);
                            $parcelIndex = 3;
                            $weightIndex = 4;
                            $codIndex = 5;
                            $revenueIndex = 6;
                            foreach ($details['months'] as $m){
                                $parcelIndexl = Coordinate::stringFromColumnIndex($parcelIndex);
                                $weightIndexl = Coordinate::stringFromColumnIndex($weightIndex);
                                $codIndexl = Coordinate::stringFromColumnIndex($codIndex);
                                $revenueIndexl = Coordinate::stringFromColumnIndex($revenueIndex);
                                $sheet->setCellValue($parcelIndexl.$col,$details['parcels'][$ship_key][$m]);
                                $sheet->setCellValue($weightIndexl.$col,$details['weight'][$ship_key][$m]);
                                $sheet->setCellValue($codIndexl.$col,$details['amount'][$ship_key][$m]);
                                $sheet->setCellValue($revenueIndexl.$col,$details['revenue'][$ship_key][$m]);
                                $parcelIndex += 4;
                                $weightIndex += 4;
                                $codIndex += 4;
                                $revenueIndex += 4;
                            }
                            $col++;
                        }
                    }


                }
            }else{
                return response()->json(['failure'=>0,'error'=>'No data found!']);
            }
            $col++;
        }
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="customer_sales_report.xlsx"');
        header('Cache-Control: max-age=0');
        $file_name = "reports/customer_sales_report".Auth::id()."xlsx";
        $writer->save("$file_name");
        return response()->json(['success'=>1,'file'=>'customer_sales_report.xlsx']);
    }
    public function customer_sales_download(Request $request){
        $file_name = "/reports/customer_sales_report".Auth::id()."xlsx";

        $file = public_path().$file_name;
        $headers = array('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',);
        return Response::download($file, 'customer_sales_report.xlsx',$headers);
    }
    public function completed_delivery_notes_index(){
        $riders = Rider::all(['id','name']);
        $admins = Admin::all(['id','name']);
        return view('admin.reports.completed_delivery_notes_report')->with(['riders'=>$riders,'admins'=>$admins]);
    }
    public function completed_delivery_notes_list(Request $request){
        $deliveries = DeliveryNote::
        join('cities AS oc', 'delivery_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'delivery_notes.rider_id', '=', 'riders.id')
            ->join('routes', 'delivery_notes.route_id', '=', 'routes.id')
            ->join('admins','admins.id','=','delivery_notes.admin_id')
            ->leftjoin('admins as ub','ub.id','=','delivery_notes.updated_by')
            ->select(['delivery_notes.id as delivery_note','delivery_notes.id as delivery_note_id','oc.id as hub_id','oc.name as hub','riders.name as rider','routes.code as route','routes.start','routes.end','admins.name as assignee','ub.name as updated_by','delivery_notes.updated_at as updated_at','delivery_notes.delivered_shipments','delivery_notes.created_at','delivery_notes.total_cod_amount as amount','delivery_notes.shipments_count'])
            ->where('delivery_notes.status',1);
        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('delivery_notes.hub_id', session('hubs'));
        }

        $datatable = Datatables::of($deliveries)
            ->editColumn('delivery_note', function ($deliveries) {
                return str_pad($deliveries->delivery_note, 6, '0', STR_PAD_LEFT);
            })
            ->setRowAttr([
                'data-hub' => function($deliveries) {
                    return $deliveries->hub_id;
                },
            ])
            ->editColumn('route', function ($rider) {
                return $rider->route.' ('.$rider->start. ' to '.$rider->end.')';
            })
            ->filterColumn('route',function($query, $keyword){
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('routes.code', 'like', '%'.$keyword.'%')->orWhere('routes.start', 'like', '%'.$keyword.'%')->orWhere('routes.end', 'like', '%'.$keyword.'%');
                }

                else {
                    $query->whereRaw('false');
                }
            });
        if($rn_no = $request->get('search_dn_no')){
            $datatable->where('delivery_notes.id','=',$rn_no);
        }
        if($tracking = $request->get('search_tracking')){
            $datatable->join('delivery_note_shipments as rns','rns.delivery_note_id','=','delivery_notes.id')
                ->join('shipments as s', 'rns.shipment_id', '=', 's.id')
                ->where('s.tracking_number', '=', $tracking);
        }
        if($rider = $request->get('search_rider')){
            $datatable->where('riders.id','=',$rider);
        }
        if($created_by = $request->get('search_assigned_by')){
            $datatable->where('admins.id','=',$created_by);
        }
        if($submitted_by = $request->get('search_updated_by')){
            $datatable->where('ub.id','=',$submitted_by);
        }
        if($submission_date = $request->get('search_submission')){
            $datatable->whereDate('delivery_notes.updated_at',$submission_date);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable->whereBetween('delivery_notes.created_at', [$from,$to]);
        }
        return $datatable->make(true);

    }
    public function customer_retention_index(){
        if (session('role_id') != 1) {
            $shippers = User::whereHas('city', function($query) {
                $query->whereIn('hub_id', session('hubs'));
            })->where('status','>=',3)->get();
            $hubs = City::select('id','name')->where('id',session('hubs'))->get();

        }else{
            $shippers = User::where('status','>=',3)->get();
            $hubs = City::select('id','name')->where('hub',1)->get();

        }

        return view('admin.reports.customer_retention_report')->with(['hubs'=>$hubs,'shippers'=>$shippers]);
    }
    public function customer_retention_export_to_excel(Request $request){
        $hub = $request->city;
        $shipper_filter = $request->shipper;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $months_array = array();
        $months_array = $this->get_months($from_date,$to_date);
        unset($months_array[0]);


        $details = array();
        $shippers = array();
        $shippers['header'] = ['S.No','Client Name'];



        foreach($months_array as $month){

            $first_date  = Carbon::parse($month)->firstOfMonth();
            $last_date  = Carbon::parse($month)->lastOfMonth()->endOfDay();
            if($hub != null){
                $details['s'][$month] = User::whereDate('activated_at','<=',$first_date)->where('status',3)->where('city_id',$hub)->count();
                $details['e'][$month] = User::whereDate('activated_at','<=',$last_date)->where('status',3)->where('city_id',$hub)->count();
                $details['n'][$month] = User::whereBetween('activated_at',[$first_date,$last_date])->where('status',3)->where('city_id',$hub)->count();
            }else{
                $details['s'][$month] = User::whereDate('activated_at','<=',$first_date)->where('status',3)->count();
                $details['e'][$month] = User::whereDate('activated_at','<=',$last_date)->where('status',3)->count();
                $details['n'][$month] = User::whereBetween('activated_at',[$first_date,$last_date])->where('status',3)->count();
            }

            $n = ($details['s'][$month] != 0)? $details['s'][$month]:0;
            $details['crr'][$month] = ($n != 0)? (($details['e'][$month]-$details['n'][$month])/$n)*100 :'-';

            $shippers['header'][] = $month;

        }

        if($hub != null){
            if (session('role_id') == 1 || in_array($hub, session('hubs'))) {
                $shippers['shipper'] = User::whereHas('city', function($query) use ($hub) {
                    $query->where('hub_id', '=', $hub);
                })->where('status','>=',3)->get();
            }
            else {
                $shippers['shipper'] = '';
            }
        }
        else{
            if (session('role_id') == 1) {
                $shippers['shipper'] = User::where('status','>=',3)->get();
            }
            else {
                $shippers['shipper'] = User::whereHas('city', function($query) {
                    $query->whereIn('hub_id', session('hubs'));
                })->where('status','>=',3)->get();
            }
        }


        if($shipper_filter != null){
            if (session('role_id') == 1){
                $client_exist =User::where('id',$shipper_filter);
                if($client_exist->exists()){
                    $shippers['shipper'] = $client_exist->get();
                }
            }else{
                $client_exist =User::where('id',$shipper_filter)
                    ->whereHas('city', function($query) {
                        $query->whereIn('hub_id', session('hubs'));
                    })->where('status','>=',3);
                if($client_exist->exists()){
                    $shippers['shipper'] = $client_exist->get();
                }
            }

        }

        if(count($shippers['shipper']) > 0){
            foreach ($shippers['shipper'] as $client){
                $shippers['name'][$client->id] = $client->name;
                foreach($months_array as $month) {
                    $thisMonth = Carbon::parse($month)->month;
                    $thisYear = Carbon::parse($month)->year;
                    $shippers['parcels'][$client->id][$month] = Shipment::where('user_id', $client->id)
                        ->whereHas('shipment_journey', function($query) use ($thisMonth,$thisYear) {
                            $query->whereMonth('created_at', $thisMonth)
                                ->whereYear('created_at', $thisYear)
                                ->where('shipper_status_id', 2);
                        })->count();
                }
            }
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $style =[
            'font' =>['bold' => true],
            'alignment' =>['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => array(
                'outline' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => array('argb' => '000000'),
                ),
            ),
        ];
        $sheet->getDefaultColumnDimension()->setWidth(20);
        $sheet->getStyle('A2:Z2')->applyFromArray($style);
        $sheet->getStyle('A4')->applyFromArray($style);
//        $sheet->getStyle('A5:D5')->applyFromArray($style);
//        $sheet->getStyle('A6:D9')->applyFromArray($style);
        $sheet->mergeCells('A2:Z2');
//        $sheet->getStyle('A10:D10')->applyFromArray($style);
        $sheet->getStyle('A12:A12')->applyFromArray($style);
        $sheet->setCellValue('A2', 'Customer Retention Report');
        $sheet->setCellValue('A4', 'Summary');
        $sheet->setCellValue('A5','Months');
        $sheet->setCellValue('A7','S');
        $sheet->setCellValue('A8','E');
        $sheet->setCellValue('A9','N');
        $sheet->setCellValue('A10','CRR');
        $sheet->setCellValue('A12', 'Details');
        $monthIndexcol1 = 2;
        $monthRow = 5;
        foreach ($months_array as $month) {
            $cellIndex1 = Coordinate::stringFromColumnIndex($monthIndexcol1);
            $sheet->setCellValue($cellIndex1.$monthRow, $month);
            $sheet->setCellValue($cellIndex1.'7', $details['s'][$month]);
            $sheet->setCellValue($cellIndex1.'8', $details['e'][$month]);
            $sheet->setCellValue($cellIndex1.'9', $details['n'][$month]);
            $sheet->setCellValue($cellIndex1.'10', $details['crr'][$month]);
            $monthIndexcol1 += 1;
        }
        $sheet->fromArray($shippers['header'],NULL,'A13');
        $col = 14;
        $serials = 1;
        $dateIndex = 3;
        if(count($shippers['shipper']) > 0) {
            foreach ($shippers['name'] as $id => $shipper) {
                $sheet->setCellValue('A' . $col, $serials);
                $sheet->setCellValue('B' . $col, $shipper);
                foreach ($months_array as $m) {
                    $cellIndexShipper = Coordinate::stringFromColumnIndex($dateIndex);
                    $sheet->setCellValue($cellIndexShipper . $col, $shippers['parcels'][$id][$m]);
                    $dateIndex++;
                }
//            $sheet->setCellValue($dateIndex.$col,$shippers['total'][$id]);
                $col++;
                $serials++;
                $dateIndex = 3;
            }
        }
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="customer_retention_report.xlsx"');
        header('Cache-Control: max-age=0');
        $file_name = "reports/customer_retention_report".Auth::id()."xlsx";
        $writer->save("$file_name");
        return response()->json(['success'=>1,'file'=>'customer_retention_report.xlsx']);

    }
    public function customer_retention_download(Request $request){
        $file_name = "/reports/customer_retention_report".Auth::id()."xlsx";
        $file = public_path().$file_name;
        $headers = array('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',);
        return Response::download($file, 'customer_retention_report.xlsx',$headers);
    }
    public function overall_sales_index(){
        $shippers = User::whereIn('status',[3,4])->select('id','name')->get();
        $cities = City::all('id','name');
        $hubs = City::where('hub',1)->select('id','name')->get();
        $statuses = ShipmentStatus::whereNotIn('id',[1,17])->get();
        return view('admin.reports.overall_sales')->with(['shippers'=>$shippers,'cities'=>$cities,'hubs'=>$hubs,'statuses'=>$statuses]);
    }
    public function overall_sales_list(Request $request){
        $sales = Shipment::join('users as u','u.id','=','shipments.user_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=' , 'sps.id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftjoin('pending_payment_shipments as pps', 'shipments.id', '=', 'pps.shipment_id')
            ->select('shipments.tracking_number','u.id as account_no','u.name as shipper','ss.name as current_status','bt.booking_type as service_type','sj.created_at as arrival_date','oc.name as origin','dc.name as destination','h.name as hub','sps.name as payment_status','shipments.amount as collection_amount','shipments.actual_weight','shipments.weight_charges','shipments.cash_handling_charges','shipments.insurance_charges','shipments.return_charges','shipments.replacement_charges','shipments.fuel_surcharge','shipments.try_and_buy_charges','shipments.packaging_material_charges','pps.gst','pps.charges as total_charges','pps.payable as net_payable')
            ->whereNotIn('shipments.shipper_status_id',[1,17]);
        if (session('role_id') != 1) {
            $sales = $sales->whereIn('dc.hub_id', session('hubs'));
        }
        $datatable = Datatables::of($sales);
        if($tracking = $request->get('search_tracking')){
            $datatable->where('shipments.tracking_number', '=', $tracking);
        }
        if($shipper = $request->get('search_shipper')){
            $datatable->where('u.id', '=', $shipper);
        }
        if($origin = $request->get('search_origin')){
            $datatable->where('oc.id', '=', $origin);
        }
        if($destination = $request->get('search_destination')){
            $datatable->where('dc.id', '=', $destination);
        }
        if($hub = $request->get('search_hub')){
            $datatable->where('h.id', '=', $hub);
        }
        if($status = $request->get('search_status')){
            $datatable->where('ss.id', '=', $status);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable->whereBetween('shipments.created_at', [$from,$to]);
        }
        return $datatable->make(true);
    }
}
