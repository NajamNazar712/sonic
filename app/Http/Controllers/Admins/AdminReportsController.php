<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\City;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use PHPExcel_Cell;
use PHPExcel_Style_Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Yajra\Datatables\Datatables;

class AdminReportsController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }
    public function qsr_index(Request $request){
        $shippers = DB::connection('reports')->table('users')->whereIn('status',[3,4])->select('id','name')->get();
        $cities = DB::connection('reports')->table('cities')->select('id','name')->get();
        $hubs = DB::connection('reports')->table('cities')->where('hub',1)->select('id','name')->get();
        return view('admin.reports.qsr_report')->with(['shippers'=>$shippers,'cities'=>$cities,'hubs'=>$hubs]);
    }
    public function qsr_list(Request $request){
        $shipments = DB::connection('reports')->table('shipments')->join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id','=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipments_journey as journey', function ($join) {
                $join->on('journey.shipment_id', '=', 'shipments.id')
                    ->where('journey.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->leftjoin('shipment_items as si', function ($join) {
                $join->on('si.shipment_id', '=', 'shipments.id')
                    ->where('si.type','=',0);
            })
            ->leftjoin('products as p','p.id','=','si.product_type_id')
            ->select(['p.product_name as product_type','si.description as description','shipments.id as shId','shipments.tracking_number','shipments.tracking_number as tracking_number_link','u.name as shipper','ss.name as history_status','bt.booking_type as service_type','sj.created_at as arrival','oc.name as origin','dc.name as destination','h.name as hub','shipments.amount','journey.created_at as last_status_date','shipments.consignee_name as name', 'shipments.booking_type_id', 'usi.poc','u.id as account_no'])
            ->whereNotIn('shipments.shipper_status_id',[1,14,16,17,25,31,36,38,39,40,41,43,47]);
        if (session('role_id') != 1) {
            $shipments = $shipments->where(function($query) {
                $query->where(function ($sub_query){
                    $sub_query->whereIn('dc.hub_id', session('hubs'));
                })
                    ->orWhere(function ($sub_query){
                        $sub_query->whereIn('oc.hub_id', session('hubs'));
                    });
            });
        }
        if(session('department_id') == 7){
            if(session('role_id') != 4 ){
                $shipments = $shipments->whereIn('shipments.user_id', session('tagged_shippers'));
            }
        }
        $datatable = Datatables::of($shipments)
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->editColumn('account_no', function ($shipments) {
                return str_pad($shipments->account_no, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper .' (' . $shipment->poc . ')';
                }
                else {
                    return $shipment->shipper;
                }
            })
            ->filterColumn('u.name', function ($query, $keyword) {
                $query->where(function ($sub_query) use ($keyword) {
                    $sub_query->where('shipments.booking_type_id', '!=', 4)
                        ->where('u.name', 'like', '%' . $keyword . '%');
                })
                    ->orWhere(function ($sub_query) use ($keyword) {
                        $sub_query->where('shipments.booking_type_id', '=', 4)
                            ->where('usi.poc', 'like', '%' . $keyword . '%');
                    });
            })
            ->addColumn('aging',function ($shipments){

                $days = Carbon::now()->diffInDays($shipments->arrival);
                if($days == 0){
                    return "-";
                }else{
                    return $days;
                }
            })
            ->addColumn('aging_last_status',function ($shipments){

                $days = Carbon::now()->diffInDays($shipments->last_status_date);
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
        if ($search_qsr = $request->get('search_qsr')) {
            if($search_qsr != 3){
                if($search_qsr == 1){
                    $datatable->whereIn('shipments.shipper_status_id',[2,3,4,5,6,7,8,9,10,11,12,13,15,18,49,51,52,54,55]);
                }
                if($search_qsr == 2){
                    $datatable->whereIn('shipments.shipper_status_id',[20, 21,22,23,24,26,27,28,29,32,33,34,35,37,44,45,46,47,48,50]);
                }
            }
        }
        if ($request->get('search_from') && $request->get('search_to')) {
            $from = $request->get('search_from');
            $to = $request->get('search_to');
            $datatable->whereBetween('sj.created_at', [$from,$to]);
        }

        return $datatable->make(true);
    }
    public function return_note_index(Request $request){
        $riders = DB::connection('reports')->table('riders')->get(['id','name']);
        $admins = DB::connection('reports')->table('admins')->get(['id','name']);
        $hubs = DB::connection('reports')->table('cities')->where('hub',1)->where('status',1)->select('id','name')->get();
        return view('admin.reports.return_notes_report')->with(['riders' => $riders, 'admins' => $admins, 'hubs' => $hubs]);
    }
    public function return_note_list(Request $request){
        $return_note = DB::connection('reports')->table('return_notes')
            ->join('cities AS oc', 'return_notes.hub_id', '=', 'oc.id')
            ->join('riders','riders.id','=','return_notes.rider_id')
            ->leftjoin('return_note_shipments as rns','rns.return_note_id', '=', 'return_notes.id')
            ->leftjoin('shipments','shipments.id', '=', 'rns.shipment_id')
            ->join('admins as cr','cr.id','=','return_notes.admin_id')
            ->leftjoin('admins as up','up.id','=','return_notes.updated_by')
            ->select(['return_notes.id','return_notes.id as return_note_id','return_notes.id as return_note_link','up.name as updated_by','return_notes.shipments_count','return_notes.shipments_count as shipments_count_link','return_notes.updated_at','return_notes.updated_at as submission_date','riders.name as rider','cr.name as created_by','return_notes.created_at as created_at','return_notes.image'])->groupBy('return_notes.id');
        if (session('role_id') != 1) {
            $return_note = $return_note->whereIn('return_notes.hub_id', session('hubs'));
        }
        if(session('department_id') == 7){
            if(session('role_id') != 4 ){
                $return_note = $return_note->whereIn('shipments.user_id', session('tagged_shippers'));
            }
        }
        $return = Datatables::of($return_note)
            ->addColumn('aging',function ($return_note){
                return ($return_note->submission_date && $return_note->created_at)? with(new Carbon($return_note->submission_date, 'UTC'))->diffInDays($return_note->created_at) :'-';
            })
            ->editColumn('return_note_id', function ($return_note) {
                return str_pad($return_note->return_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('return_note_link', function($return_note) {
                return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($return_note->id, 6, '0', STR_PAD_LEFT) . '</span></button>';
            })
            ->editColumn('image', function ($return_note) {
                $now = Carbon::now();
                if ($return_note->image != null && ($now->diffInDays($return_note->updated_at) < 30)) {
                    $img = asset('uploads/return_notes/' . $return_note->image);
                    return "<a href='{$img}' class='btn btn-block btn-outline-info mr-1' target='_blank'><i class='la la-image'></i></a>";

                } else {
                    return "-";
                }

            })
            ->editColumn('shipments_count_link', function($return_note) {
                if ($return_note->shipments_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $return_note->shipments_count . '</button>';
                }
                else {
                    return 0;
                }
            });

        if($rn_no = $request->get('search_rn_no')){
            $return->where('return_notes.id','=',$rn_no);
        }
        if($tracking = $request->get('search_tracking')){
            $return->join('return_note_shipments as rnst','rnst.return_note_id','=','return_notes.id')
                ->join('shipments as s', 'rnst.shipment_id', '=', 's.id')
                ->where('s.tracking_number', '=', $tracking);
        }
        if($rider = $request->get('search_rider')){
            $return->where('riders.id','=',$rider);
        }
        if ($hub = $request->get('search_hub')) {
            $return->where('oc.id', '=', $hub);
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
    //Return Note Print
    public function return_note_shipments(Request $request){
        $return_note_id = $request->input('return_note_id');
        $return_note_shipments = DB::connection('reports')->table('return_note_shipments')->where('return_note_id', $return_note_id)->get();
        $shipments = array();
        if($return_note_shipments->count() != 0){
            foreach ($return_note_shipments as $return_note_shipment){
                $shipment = DB::connection('reports')->table('shipments')->find($return_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Return Note Shipments', 'shipments' => $shipments];
        }else{
            return ['status' => 0, 'success' => 'No Return Note Shipments', 'shipments' => FALSE];
        }
    }
    public function pickup_note_index(Request $request){
        $riders = DB::connection('reports')->table('riders')->get(['id','name']);
        $admins = DB::connection('reports')->table('admins')->get(['id','name']);
        $cities = DB::connection('reports')->table('cities')->get(['id','name']);
        return view('admin.reports.pickup_notes_report')->with(['riders'=>$riders,'admins'=>$admins,'cities'=>$cities]);
    }
    public function pickup_note_list(Request $request){
        $pickup_note = DB::connection('reports')->table('pickup_notes')->join('cities','cities.id','=','pickup_notes.city_id')
            ->leftjoin('riders','riders.id','=','pickup_notes.rider_id')
            ->leftjoin('admins as ab','ab.id','=','pickup_notes.assigned_by_user_id')
            ->leftjoin('admins as up','up.id','=','pickup_notes.updated_by')
            ->leftjoin('pickup_note_requests as pnr','pnr.pickup_note_id', '=', 'pickup_notes.id')
            ->leftjoin('pickup_requests as pr', 'pr.id', '=', 'pnr.pickup_request_id')
            ->select(['pr.shipper_id','pickup_notes.id as pn_id','pickup_notes.id as pickup_note_no','cities.name as city','pickup_notes.pickups', DB::connection('reports')->raw('(select SUM(received) as received from pickup_requests where pickup_requests.id in (select pickup_request_id from pickup_note_requests where pickup_note_id = pickup_notes.id)) AS received'), 'riders.name as rider','pickup_notes.created_at as assigned_date','ab.name as assigned_by','pickup_notes.updated_at as completed_date','up.name as completed_by'])
            ->where('pickup_notes.status_id',4);
        if (session('role_id') != 1) {
            $pickup_note = $pickup_note->whereIn('cities.hub_id', session('hubs'));
        }
        if(session('department_id') == 7){
            if(session('role_id') != 4 ){
                $pickup_note = $pickup_note->whereIn('pr.shipper_id', session('tagged_shippers'));
            }
        }
        $pickup_note = Datatables::of($pickup_note)
            ->editColumn('pn_id', function ($pickup_note) {
                return str_pad($pickup_note->pn_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('pickup_note_no', function($pickup_note) {
                return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($pickup_note->pickup_note_no, 6, '0', STR_PAD_LEFT) . '</span></button>';
            })
            ->addColumn('aging', function($pickup_note) {
                $assigned_date = Carbon::parse($pickup_note->assigned_date)->startOfDay();

                $completed_date = Carbon::parse($pickup_note->completed_date)->startOfDay();

                return $assigned_date->diffInDays($completed_date) . 'd';
            })
            ->addColumn('bookings_link', function($pickup_notes) {
                if ($pickup_notes->received != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $pickup_notes->received . '</button>';
                }
                else {
                    return 0;
                }
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
    public function pickup_note_bookings(Request $request){
        $pickup_note_id = $request->input('pickup_note_id');

        $pickup_note_requests = DB::connection('reports')->table('pickup_note_requests')->where('pickup_note_id', $pickup_note_id)->get();

        if ($pickup_note_requests->count() != 0){
            $pickup_request_all_received_shipments = array();
            $bookings = array();

            foreach($pickup_note_requests as $pickup_note_request) {
                $pickup_request = DB::connection('reports')->table('pickup_requests')->find($pickup_note_request->pickup_request_id);
                $shipper = DB::connection('reports')->table('users')->find($pickup_request->shipper_id)->name;
                $pickup_request_all_received_shipments[$shipper] = DB::connection('reports')->table('pickup_request_received_shipments')->where('pickup_request_id', $pickup_request->id)->get();
                if ($pickup_request_all_received_shipments[$shipper]->count() != 0) {

                    foreach ($pickup_request_all_received_shipments[$shipper] as $all_shipments) {
                        $shipment = $all_shipments->shipment_id;
                        $shipment_details = DB::connection('reports')->table('shipments')->find($shipment);
                        $bookings[$shipper][] = $shipment_details->tracking_number;
                    }
                }

            }
            return ['status' => 0, 'success' => 'Booked Shipments', 'booked' => $bookings];

        }else{
            return ['status' => 0, 'success' => 'No Pickup requests found', 'booked' => FALSE];
        }


    }

    public function cargo_received_index(Request $request){
        $shippimg_modes = DB::connection('reports')->table('shipping_modes')->get();
        $cities = DB::connection('reports')->table('cities')->select('id','name')->where('hub',1)->get();
        return view('admin.reports.cargo_received_report')->with(['cities'=>$cities,'shippimg_modes'=>$shippimg_modes]);
    }
    public function cargo_received_list(Request $request){
        $cargo_received = DB::connection('reports')->table('cargo_consignments')->join('cities as oc','oc.id','=','cargo_consignments.origin_hub_id')
            ->join('cities as h','h.id','=','cargo_consignments.destination_hub_id')
            ->join('shipping_modes as sm','sm.id','=','cargo_consignments.shipping_mode_id')
            ->join('admins as si','si.id','=','cargo_consignments.sender_id')
            ->join('admins as ri','ri.id','=','cargo_consignments.receiver_id')
            ->select(['cargo_consignments.id as cargo_id','cargo_consignments.id as cargo_id_link','oc.name as origin','h.name as destination','cargo_consignments.shipments','cargo_consignments.shipments as shipments_link','sm.mode as shipping_mode','cargo_consignments.shipments_weight', DB::connection('reports')->raw('(SELECT SUM(`s`.`chargeable_weight`) FROM `shipments` AS `s` INNER JOIN `cargo_consignment_shipments` AS `css` ON `s`.`id` = `css`.`shipment_id` WHERE `css`.`cargo_consignment_id` = `cargo_consignments`.`id`) AS `chargeable_weight`'), 'cargo_consignments.actual_weight', 'cargo_consignments.vendor_weight', 'cargo_consignments.created_at as transit_at','si.name as transit_by','ri.name as received_by','cargo_consignments.updated_at as received_at','cargo_consignments.received_shipments','cargo_consignments.type as cargo_type','cargo_consignments.seal_number'])
            ->where('cargo_consignments.status_id',3);
        if (session('role_id') != 1) {
            $cargo_received = $cargo_received->where(function ($query) {
                $query->whereIn('oc.hub_id', session('hubs'))->orWhereIn('h.hub_id', session('hubs'));
            });
        }
        $cargo = Datatables::of($cargo_received)
            ->addColumn('aging',function ($cargo_received){
                return ($cargo_received->received_at && $cargo_received->transit_at)? Carbon::parse($cargo_received->received_at)->diffInDays($cargo_received->transit_at) :'-';
            })
            ->editColumn('cargo_id_link', function ($cargo_received) {
                $print_cargo = "<u><a href='javascript:void(0);' class='cargo_print'>" .str_pad($cargo_received->cargo_id, 6, '0', STR_PAD_LEFT)."</a></u>";
                return $print_cargo;
            })
            ->editColumn('cargo_type',function ($cargo_received){
                if($cargo_received->cargo_type == 1){
                    return 'Normal';
                }else{
                    return 'Return';
                }
            })
            ->editColumn('shipments_link',function ($cargo_received){
                return '<button class="btn btn-sm btn-outline-info align-middle">' . $cargo_received->shipments . '</button>';
            });

        if($cargo_no = $request->get('search_cargo_no')){
            $cargo->where('cargo_consignments.id','=',$cargo_no);
        }
        if($tracking = $request->get('search_tracking')){
            $cargo->join('cargo_consignment_shipments as ccs','ccs.cargo_consignment_id','=','cargo_consignments.id')
                ->join('shipments as s', 'ccs.shipment_id', '=', 's.id')
                ->where('s.tracking_number', '=', $tracking);
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
        if ($cargo_type = $request->get('cargo_type')) {
            if ($cargo_type != 0) {
                $cargo->where('cargo_consignments.type', $cargo_type);
            }
        }
        return $cargo->make(true);
    }
    public function cargo_shipments(Request $request) {
        $tracking_numbers = array();

        $cargo_consignments_shipments = DB::connection('reports')->table('cargo_consignment_shipments')->where('cargo_consignment_id', $request->id)->get();

        foreach ($cargo_consignments_shipments as $cargo_consignments_shipment) {
            $shipment = DB::connection('reports')->table('shipments')->find($cargo_consignments_shipment->shipment_id);

            $tracking_number = array();

            $tracking_number['tracking_number'] = $shipment->tracking_number;
            $tracking_number['estimated_weight'] = $shipment->estimated_weight;
            $tracking_number['actual_weight'] = $shipment->actual_weight;
            $tracking_number['chargeable_weight'] = ($shipment->chargeable_weight) ? $shipment->chargeable_weight : '';

            $tracking_numbers[] = $tracking_number;
        }

        return $tracking_numbers;
    }

    public function lead_time_index(Request $request){
//        $shippers = User$generator::all(['id','name']);
        $cities = DB::connection('reports')->table('cities')->get(['id','name']);
        $shipper = DB::connection('reports')->table('users')->get(['id','name']);
        $hubs = DB::connection('reports')->table('cities')->select(['id','name'])->where('hub',1)->get();
        $statuses = DB::connection('reports')->table('shipment_status')->get(['id','name']);
        return view('admin.reports.lead_time_report')->with(['cities'=>$cities,'statuses'=>$statuses,'hubs'=>$hubs,'shipper'=>$shipper]);
    }
    public function lead_time_list(Request $request){
        $shipments = DB::connection('reports')->table('shipments')->join('users as u','u.id','=','shipments.user_id')
//            ->join('user_bank_infos as ubi','ubi.user_id','=','u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=' , 'sps.id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id','=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipments_journey as radd', function ($join) {
                $join->on('radd.shipment_id', '=', 'shipments.id')
                    ->where('radd.id','=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 4)'));
            })
            ->leftJoin('shipments_journey as fstatus', function ($join) {
                $join->on('fstatus.shipment_id', '=', 'shipments.id')
                    ->where('fstatus.id','>',
                        DB::connection('reports')->raw('(select min(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 5)'));
            })
            ->leftJoin('shipments_journey as lstatus', function ($join) {
                $join->on('lstatus.shipment_id', '=', 'shipments.id')
                    ->where('lstatus.id','=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.verification = 0)'));
            })
            ->leftJoin('shipments_journey as dd', function ($join) {
                $join->on('dd.shipment_id', '=', 'shipments.id')
                    ->where('dd.id','=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,16,30,36) )'));
            })
            ->leftJoin('shipments_journey as rc', function ($join) {
                $join->on('rc.shipment_id', '=', 'shipments.id')
                    ->where('rc.id','=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(20,42))'));
            })
            ->leftJoin('shipments_journey as rrad', function ($join) {
                $join->on('rrad.shipment_id', '=', 'shipments.id')
                    ->where('rrad.id','=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 22)'));
            })
            ->leftJoin('shipments_journey as rds', function ($join) {
                $join->on('rds.shipment_id', '=', 'shipments.id')
                    ->where('rds.id','=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(24,25,29,31,35,38))'));
            })
            ->leftJoin('shipments_journey as pd', function ($join) {
                $join->on('pd.shipment_id', '=', 'shipments.id')
                    ->where('pd.id','=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(39,40,41,43))'));
            })
            ->leftJoin('shipments_journey as ret_or_del', function ($join) {
                $join->on('ret_or_del.shipment_id', '=', 'shipments.id')
                    ->where('ret_or_del.id','=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,20,30,36,37,42))'));
            })
            ->leftJoin('shipments_journey as lj', function ($join) {
                $join->on('lj.shipment_id', '=', 'shipments.id')
                    ->where('lj.id','=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id)'));
            })
            ->leftJoin('delivery_note_shipments as dns', function ($join) {
                $join->on('dns.shipment_id', '=', 'shipments.id')
                    ->where('dns.delivery_note_id','=',
                        DB::connection('reports')->raw('(select min(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = shipments.id)'));
            })
            ->leftJoin('shipments_journey as fatstatus', function ($join) {
                $join->on('fatstatus.shipment_id', '=', 'shipments.id')
                    ->where('fatstatus.id','=',
                        DB::connection('reports')->raw('(select min(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 5)'));
            })
            ->leftJoin('shipment_status as fs','fs.id','=','fstatus.shipper_status_id')
            ->leftJoin('shipment_status as ls','ls.id','=','lstatus.shipper_status_id')
            ->leftJoin('shipment_status as rdss','rdss.id','=','rds.shipper_status_id')
            ->leftjoin('delivery_note_shipments as dnss', function ($join) {
                $join->on('dnss.shipment_id', '=', 'shipments.id')
                    ->where('dnss.delivery_note_id','=',
                        DB::connection('reports')->raw('(select max(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = shipments.id)'));
            })
//            ->leftjoin('shipment_status as sss','sss.id','=','lsj.shipper_status_id')
            ->leftjoin('cargo_consignment_shipments as ccs', function($join){
                $join->on('ccs.shipment_id','=','shipments.id')
                    ->leftjoin('cargo_consignments as cc','cc.id','=','ccs.cargo_consignment_id')
                    ->leftjoin('cargo_consignment_junction_receivals as ccjr', 'ccjr.junction_id', '=', 'cc.junction_hub_1_id');
            })
//            ->leftjoin('transport_mode_vendor as tmv','tmv.id','=','cc.transport_mode_vendor_id')
            ->leftjoin('delivery_note_shipments as dnssaa', function ($join) {
                $join->on('dnssaa.shipment_id', '=', 'shipments.id')
                    ->where('dnssaa.delivery_note_id','=',
                        DB::connection('reports')->raw('(select max(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = shipments.id)'));
            })
            ->leftjoin('delivery_note_shipments as fdnsv', function($join){
                $join->on('fdnsv.shipment_id','=','shipments.id')
                    ->where('fdnsv.delivery_note_id','=',
                        DB::connection('reports')->raw('(select min(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = shipments.id)'));
            })
//            ->leftjoin('shipments_journey as lss','lss.shipper_status_id','=',[7, 8, 9, 10, 11, 12, 15, 18])
            ->leftjoin('delivery_note_shipments as ldnsv', function($join){
                $join->on('ldnsv.shipment_id','=','shipments.id')
                    ->where('ldnsv.delivery_note_id','=',
                        DB::connection('reports')->raw('(select max(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = shipments.id)'));
            })
            ->leftjoin('shipments_journey as fsjv',function($join) {
                $join->on('fsjv.reference_1_id', '=', 'fdnsv.delivery_note_id')
                    ->where('fsjv.id','=', DB::connection('reports')->raw('(select min(id) from shipments_journey where shipments_journey.reference_1_id = fdnsv.delivery_note_id and shipments_journey.shipper_status_id > 5 and shipments_journey.verification = 1)'));
            })
            ->leftjoin('shipment_status as fssv','fssv.id','=','fsjv.shipper_status_id')
            ->leftjoin('shipments_journey as lsjv',function($join) {
                $join->on('lsjv.reference_1_id', '=', 'ldnsv.delivery_note_id')
                    ->where('lsjv.id','=', DB::connection('reports')->raw('(select max(id) from shipments_journey  where shipments_journey.reference_1_id = ldnsv.delivery_note_id and shipments_journey.verification = 1)'));
            })
            ->leftjoin('cargo_consignment_shipments as cccc',function($join){
                $join->on('cccc.shipment_id', '=', 'shipments.id')
                    ->where('cccc.id', '=', DB::connection('reports')->raw('(select min(id) from cargo_consignment_shipments where cargo_consignment_shipments.shipment_id = shipments.id)'));
            })
            ->leftjoin('cargo_consignment_shipments as ccrc',function($join){
                $join->on('ccrc.shipment_id', '=', 'shipments.id')
                    ->where('ccrc.id', '=', DB::connection('reports')->raw('(select max(id) from cargo_consignment_shipments where cargo_consignment_shipments.shipment_id = shipments.id)'));
            })            ->leftjoin('cargo_consignments as ccss','ccss.id', '=', 'cccc.cargo_consignment_id')
            ->leftjoin('cargo_consignments as ccssr','ccssr.id', '=', 'ccrc.cargo_consignment_id')
            ->leftjoin('shipment_status as lssv','lssv.id','=','lsjv.shipper_status_id')
            ->select('fatstatus.created_at as first_attempt','ccjr.created_at as junction','cc.transport_mode_vendor_id as vendor','fssv.name as first_verification','lssv.name as last_verification','fsjv.created_at as verification_status_date', 'lsjv.created_at as last_verification_status_date','dns.delivery_note_id as first_delivery_note_id','dnss.delivery_note_id as last_delivery_note_id','shipments.id as Shipment_id','shipments.tracking_number','shipments.created_at as cd','shipments.tracking_number as tracking_number_link','u.id as account_no','u.name as shipper','oc.name as origin','dc.name as destination','h.name as hub','ss.name as current_status','sj.created_at as arrival_date','radd.created_at as reached_at_destination','fstatus.created_at as first_status_date','lstatus.created_at as last_status_date','fs.name as first_status','ls.name as last_status','dd.created_at as delivered_date','rc.created_at as return_confirm','rrad.created_at as return_reached_at_destination','rds.created_at as return_delivered_date','rdss.name as return_delivered_status','pd.created_at as payment_done_date','shipments.shipper_status_id','ret_or_del.shipper_status_id as return_check','lj.created_at as latest_journey_date','sps.name as payment_status', 'shipments.booking_type_id', 'usi.poc', 'ccss.id as cargo_number', 'ccss.created_at as cargo_date_time', 'ccssr.type as return_type', 'ccssr.id as return_cargo_number', 'ccssr.created_at as return_cargo_date_time')
            ->groupBy('shipments.id');
        if (session('role_id') != 1) {
            $shipments = $shipments->whereIn('dc.hub_id', session('hubs'));
        }
        $lead_time = Datatables::of($shipments)
            ->editColumn('account_no', function ($shipments) {
                return str_pad($shipments->account_no, 6, '0', STR_PAD_LEFT);
            })
            ->editcolumn('vendor',function ($shipments){
                if($shipments->vendor != null) {
                    $name = DB::connection('reports')->table('transport_mode_vendors')->where('transport_mode_vendors.id', $shipments->vendor)->first();
                    return $name->name;
                }
                else
                {
                    return "-";
                }
            })
            ->editColumn('return_cargo_number', function ($shipment){
                if($shipment->return_type == 2){
                    return $shipment->return_cargo_number;
                }
                else{
                    return "-";
                }
            })
            ->editColumn('return_cargo_date_time', function ($shipment){
                if($shipment->return_type == 2){
                    return $shipment->return_cargo_date_time;
                }
                else{
                    return "-";
                }
            })
            ->editColumn('cargo_number', function ($shipment){
                if($shipment->cargo_number != null){
                    return $shipment->cargo_number;
                }
                else{
                    return "-";
                }
            })
            ->editColumn('cargo_date_time', function ($shipment){
                if($shipment->cargo_date_time != null){
                    return $shipment->cargo_date_time;
                }
                else{
                    return "-";
                }
            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper .' (' . $shipment->poc . ')';
                }
                else {
                    return $shipment->shipper;
                }
            })
            ->filterColumn('u.name', function ($query, $keyword) {
                $query->where(function ($sub_query) use ($keyword) {
                    $sub_query->where('shipments.booking_type_id', '!=', 4)
                        ->where('u.name', 'like', '%' . $keyword . '%');
                })
                    ->orWhere(function ($sub_query) use ($keyword) {
                        $sub_query->where('shipments.booking_type_id', '=', 4)
                            ->where('usi.poc', 'like', '%' . $keyword . '%');
                    });
            })
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->filterColumn('shipper', function ($query, $keyword) {
                return $query->where('u.name', '=', $keyword);
            })
            ->addColumn('transit_tat',function ($shipments){
                return ($shipments->arrival_date && $shipments->reached_at_destination)? with((new Carbon($shipments->arrival_date, 'UTC'))->diffInWeekendDays($shipments->reached_at_destination)-(new Carbon($shipments->arrival_date, 'UTC'))->diffInDaysFiltered(function (Carbon $date){
                        $date->isSunday();
                    },$shipments->reached_at_destination)):'-';
            })
            ->addColumn('attempt_tat',function ($shipments){
                return ($shipments->arrival_date && $shipments->first_status_date)? with((new Carbon($shipments->arrival_date, 'UTC'))->diffInWeekendDays($shipments->first_status_date)-(new Carbon($shipments->arrival_date, 'UTC'))->diffInDaysFiltered(function (Carbon $date){
                        $date->isSunday();
                    },$shipments->first_status_date)):'-';
            })
            ->addColumn('delivered_tat',function ($shipments){
                return ($shipments->arrival_date && $shipments->delivered_date)? with((new Carbon($shipments->arrival_date, 'UTC'))->diffInWeekendDays($shipments->delivered_date)-(new Carbon($shipments->arrival_date, 'UTC'))->diffInDaysFiltered(function (Carbon $date){
                        $date->isSunday();
                    },$shipments->delivered_date)):'-';
            })
            ->addColumn('dispatch_tat',function ($shipments){
                return ($shipments->reached_at_destination && $shipments->first_status_date)? with((new Carbon($shipments->reached_at_destination, 'UTC'))->diffInWeekendDays($shipments->first_status_date)-(new Carbon($shipments->reached_at_destination, 'UTC'))->diffInDaysFiltered(function (Carbon $date){
                        $date->isSunday();
                    },$shipments->first_status_date)):'-';
            })
            ->addColumn('return_transit_tat',function ($shipments){
                return ($shipments->return_confirm && $shipments->return_reached_at_destination)? with((new Carbon($shipments->return_confirm, 'UTC'))->diffInWeekendDays($shipments->return_reached_at_destination)-(new Carbon($shipments->return_confirm, 'UTC'))->diffInDaysFiltered(function (Carbon $date){
                        $date->isSunday();
                    },$shipments->return_reached_at_destination)):'-';
            })
            ->addColumn('return_dispatch_tat',function ($shipments){
                return ($shipments->return_delivered_date && $shipments->return_reached_at_destination)? with((new Carbon($shipments->return_reached_at_destination, 'UTC'))->diffInWeekendDays($shipments->return_delivered_date)-(new Carbon($shipments->return_reached_at_destination, 'UTC'))->diffInDaysFiltered(function (Carbon $date){
                        $date->isSunday();
                    },$shipments->return_delivered_date)):'-';
            })
            ->addColumn('return_tat',function ($shipments){
                return ($shipments->return_confirm && $shipments->return_delivered_date)? with((new Carbon($shipments->return_confirm, 'UTC'))->diffInWeekendDays($shipments->return_delivered_date)-(new Carbon($shipments->return_confirm, 'UTC'))->diffInDaysFiltered(function (Carbon $date){
                        $date->isSunday();
                    },$shipments->return_delivered_date)):'-';
            })
            ->addColumn('payment_tat',function ($shipments){
                $return = array(20,42);
                if(in_array($shipments->return_check,$return)){
                    return ($shipments->return_delivered_date && $shipments->payment_done_date)? with((new Carbon($shipments->return_delivered_date, 'UTC'))->diffInWeekendDays($shipments->payment_done_date)-(new Carbon($shipments->return_delivered_date, 'UTC'))->diffInDaysFiltered(function (Carbon $date){
                            $date->isSunday();
                        },$shipments->payment_done_date)):'-';
                }else{
                    return ($shipments->delivered_date && $shipments->payment_done_date)? with((new Carbon($shipments->delivered_date, 'UTC'))->diffInWeekendDays($shipments->payment_done_date)-(new Carbon($shipments->delivered_date, 'UTC'))->diffInDaysFiltered(function (Carbon $date){
                            $date->isSunday();
                        },$shipments->payment_done_date)):'-';
                }
            })
            ->addColumn('total_tat',function ($shipments){
                return ($shipments->arrival_date && $shipments->latest_journey_date)? with((new Carbon($shipments->arrival_date, 'UTC'))->diffInWeekDays($shipments->latest_journey_date)-(new Carbon($shipments->arrival_date, 'UTC'))->diffInDaysFiltered(function (Carbon $date){
                        $date->isSunday();
                    },$shipments->latest_journey_date)):'-';
            });
        if($tracking = $request->get('search_tracking_no')){
            $lead_time->where('shipments.tracking_number', '=', $tracking);
        }
        if($shipper = $request->get('search_shipper')){
            $lead_time->where('u.id', '=', $shipper);
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
            $stations = DB::connection('reports')->table('cities')->whereIn('hub_id', session('hubs'))->get();
        }else{
            $stations = DB::connection('reports')->table('cities')->where('hub',1)->select('id','name')->get();
        }
        foreach ($stations as $hub) {
            $qa_data[$hub->name]['cargo_pending'] = DB::connection('reports')->table('shipments')
                ->whereExists(function($query) use ($hub) {
                    $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function($sub_query) use ($hub) {
                        $sub_query->from('cities')
                        ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                        ->where('cities.id', $hub->id);
                    });
                })
                ->whereExists(function ($query) use ($hub) {
                    $query->from('cities')
                    ->where('shipments.consignee_city_id', '=', DB::raw('`cities`.`id`'))
                    ->where('hub_id', '=', $hub->id);
                })->whereExists(function ($query) use ($from, $to) {
                    $query->from('shipments_journey')
                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                    ->whereBetween('created_at', [$from, $to])
                    ->where('shipper_status_id', 2);
                })->count();
            $qa_data[$hub->name]['cargo_resolved'] = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                    $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function($sub_query) use ($hub) {
                        $sub_query->from('cities')
                        ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                        ->where('cities.id', $hub->id);
                    });
                })
                ->whereExists(function ($query) use ($hub) {
                    $query->from('cities')
                    ->where('shipments.consignee_city_id', '=', DB::raw('`cities`.`id`'))
                    ->where('hub_id', '=', $hub->id);
                })->whereExists(function ($query) use ($from, $to) {
                    $query->from('shipments_journey')
                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                    ->whereBetween('created_at', [$from, $to])
                    ->where('shipper_status_id', 3);
                })->count();
            $qa_data[$hub->name]['cargo_transit_pending'] = DB::connection('reports')->table('cargo_consignments')->whereBetween('created_at',[$from,$to])->where('status_id','!=',3)->where('origin_hub_id',$hub->id)->count();
            $qa_data[$hub->name]['cargo_transit_resolved'] = DB::connection('reports')->table('cargo_consignments')->whereBetween('updated_at',[$from,$to])->where('status_id','=',3)->where('origin_hub_id',$hub->id)->count();
            $pending_status = array(2, 4, 6, 7, 8, 9, 13, 15); //for pending deliveries
            $not_pending_status = array(1, 3, 5, 10, 11,12,14,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47); //for pending deliveries
            $qa_data[$hub->name]['deliveries_pending'] = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($hub) {
                    $query->from('cities')
                    ->where('shipments.consignee_city_id', '=', DB::raw('`cities`.`id`'))
                    ->where('hub_id', '=', $hub->id);
                })
                ->whereNotExists(function ($query) use ($from, $to, $not_pending_status) {
                    $query->from('shipments_journey')
                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                    ->whereBetween('created_at', [$from, $to])
                    ->whereIn('shipper_status_id', $not_pending_status);
                })
                ->count();

            $qa_data[$hub->name]['deliveries_resolved'] = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($hub) {
                    $query->from('cities')
                    ->where('shipments.consignee_city_id', '=', DB::raw('`cities`.`id`'))
                    ->where('hub_id', '=', $hub->id);
                })
                ->whereExists(function ($query) use ($from, $to) {
                    $query->from('shipments_journey')
                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                    ->whereBetween('created_at', [$from, $to])
                    ->where('shipper_status_id', 5);
                })
                ->count();
            $qa_data[$hub->name]['receive_deliveries_pending'] = DB::connection('reports')->table('delivery_notes')->whereBetween('created_at',[$from,$to])->where('status',0)->count();
            $qa_data[$hub->name]['receive_deliveries_resolved'] = DB::connection('reports')->table('delivery_notes')->whereBetween('created_at',[$from,$to])->where('status',1)->count();
            $qa_data[$hub->name]['return_marked_pending'] = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($hub) {
                    $query->from('cities')
                    ->where('shipments.consignee_city_id', '=', DB::raw('`cities`.`id`'))
                    ->where('hub_id', '=', $hub->id);
                })
                ->whereExists(function ($query) use ($from, $to) {
                    $query->from('shipments_journey')
                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                    ->whereBetween('created_at', [$from, $to])
                    ->where('shipper_status_id', 12);
                })->count();
            $qa_data[$hub->name]['return_marked_resolved'] = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($hub) {
                    $query->from('cities')
                    ->where('shipments.consignee_city_id', '=', DB::raw('`cities`.`id`'))
                    ->where('hub_id', '=', $hub->id);
                })
                ->whereExists(function ($query) use ($from, $to) {
                    $query->from('shipments_journey')
                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                    ->whereBetween('created_at', [$from, $to])
                    ->whereIn('shipper_status_id', [13, 20]);
                })->count();
            $qa_data[$hub->name]['return_confirmed_pending'] = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($hub) {
                    $query->from('cities')
                    ->where('shipments.consignee_city_id', '=', DB::raw('`cities`.`id`'))
                    ->where('hub_id', '=', $hub->id);
                })
                ->whereExists(function ($query) use ($from, $to) {
                    $query->from('shipments_journey')
                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                    ->whereBetween('created_at', [$from, $to])
                    ->where('shipper_status_id', 20);
                })->count();
            $qa_data[$hub->name]['return_confirmed_resolved'] = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($hub) {
                    $query->from('cities')
                    ->where('shipments.consignee_city_id', '=', DB::raw('`cities`.`id`'))
                    ->where('hub_id', '=', $hub->id);
                })
                ->whereExists(function ($query) use ($from, $to) {
                    $query->from('shipments_journey')
                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                    ->whereBetween('created_at', [$from, $to])
                    ->whereIn('shipper_status_id', [21, 23]);
                })->count();
            $qa_data[$hub->name]['return_cargo_pending'] = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                    $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function($sub_query) use ($hub) {
                        $sub_query->from('cities')
                        ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                        ->where('cities.id', $hub->id);
                    });
                })
                ->whereExists(function ($query) use ($from, $to) {
                    $query->from('shipments_journey')
                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                    ->whereBetween('created_at', [$from, $to])
                    ->whereIn('shipper_status_id', [21, 26, 32]);
                })->count();
            $qa_data[$hub->name]['return_cargo_resolved'] = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                    $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function($sub_query) use ($hub) {
                        $sub_query->from('cities')
                        ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                        ->where('cities.id', $hub->id);
                    });
                })
                ->whereExists(function ($query) use ($from, $to) {
                    $query->from('shipments_journey')
                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                    ->whereBetween('created_at', [$from, $to])
                    ->whereIn('shipper_status_id', [22, 27, 33]);
                })->count();
            $qa_data[$hub->name]['return_delivery_pending'] = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                    $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function($sub_query) use ($hub) {
                        $sub_query->from('cities')
                        ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                        ->where('cities.id', $hub->id);
                    });
                })
                ->whereExists(function ($query) use ($from, $to) {
                    $query->from('shipments_journey')
                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                    ->whereBetween('created_at', [$from, $to])
                    ->whereIn('shipper_status_id', [22, 24, 27, 29, 33, 35]);
                })->count();
            $qa_data[$hub->name]['return_delivery_resolved'] = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                    $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function($sub_query) use ($hub) {
                        $sub_query->from('cities')
                        ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                        ->where('cities.id', $hub->id);
                    });
                })
                ->whereExists(function ($query) use ($from, $to) {
                    $query->from('shipments_journey')
                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                    ->whereBetween('created_at', [$from, $to])
                    ->whereIn('shipper_status_id', [22, 24, 27, 29, 33, 35]);
                })->count();
            $qa_data[$hub->name]['return_receive_pending'] = DB::connection('reports')->table('return_notes')->whereBetween('created_at',[$from,$to])->where('hub_id',$hub->id)->where('status',0)->count();
            $qa_data[$hub->name]['return_receive_resolved'] = DB::connection('reports')->table('return_notes')->whereBetween('updated_at',[$from,$to])->where('hub_id',$hub->id)->where('status',1)->count();
        }

        return $qa_data;
    }
    public function outstanding_shipments_index(Request $request){
        $hubs = DB::connection('reports')->table('cities')->where('hub',1)->select('id','name')->get();
        return view('admin.reports.outstanding_shipments_report')->with('hubs',$hubs);
    }
    public function outstanding_shipments_list(Request $request){
        $shipments = DB::connection('reports')->table('delivery_note_shipments')->join('shipments as s', 'delivery_note_shipments.shipment_id', '=', 's.id')
            ->join('cities as dc', 's.consignee_city_id', '=', 'dc.id')
            ->join('delivery_notes as delivery_note', 'delivery_note_shipments.delivery_note_id', '=', 'delivery_note.id')
            ->join('riders as rider', 'delivery_note.rider_id', '=', 'rider.id')
            ->join('cities as hc', 'dc.hub_id', '=', 'hc.id')
            ->join('users as u', 's.user_id', '=', 'u.id')
            ->join('booking_types as bt', 's.booking_type_id', '=', 'bt.id')
            ->join('user_shipping_infos AS usi', 's.pickup_address_id', '=', 'usi.id')
            ->leftjoin('shipment_payment_status as sps', 's.payment_status_id', '=' , 'sps.id')
            ->leftjoin('shipments_journey as sj', function($join) {
                $join->on('sj.shipment_id', '=', 's.id')
                    ->where('sj.id', '=', DB::connection('reports')->raw('(SELECT MAX(id) FROM shipments_journey WHERE shipment_id = s.id)'));
            })
            ->leftjoin('shipments_journey as sod', function($join) {
                $join->on('sod.shipment_id', '=', 's.id')
                    ->where('sod.id', '=', DB::connection('reports')->raw('(SELECT MAX(id) FROM shipments_journey WHERE shipment_id = s.id and shipments_journey.verification = 0 and shipments_journey.reference_1_id = delivery_note.id)'));
            })
            ->leftjoin('shipments_journey as svd', function($join) {
                $join->on('svd.shipment_id', '=', 's.id')
                    ->where('svd.id', '=', DB::connection('reports')->raw('(SELECT MAX(id) FROM shipments_journey WHERE shipment_id = s.id and shipments_journey.verification = 1 and shipments_journey.reference_1_id = delivery_note.id and shipments_journey.shipper_status_id != 5)'));
            })
            ->leftjoin('shipments_journey as sjd', function($join) {
                $join->on('sjd.shipment_id', '=', 's.id')
                    ->where('sjd.id', '=', DB::connection('reports')->raw('(SELECT MAX(id) FROM shipments_journey WHERE shipment_id = s.id AND shipper_status_id IN (14, 16, 30, 36))'));
            })
            ->join('shipment_status as ss', 'sj.shipper_status_id', '=', 'ss.id')
            ->leftjoin('delivery_note_station_deposit_notes as dnsdn', 'delivery_note_shipments.delivery_note_id', '=', 'dnsdn.delivery_note_id')
            ->select('s.id', 's.tracking_number', 's.consignee_name as consignee', 's.consignee_address as address', 'dc.name as destination', 'hc.name as hub', 'u.name as shipper', 'bt.booking_type as service_type', 's.amount', 'ss.name as current_status', 'sod.created_at as operation_status_date','svd.created_at as verification_status_date', 'sj.remarks', 'delivery_note_shipments.delivery_note_id as dncc', 'delivery_note_shipments.delivery_note_id as dncc_link', 'dnsdn.station_deposit_note_id as sdn', 'dnsdn.station_deposit_note_id as sdn_link', 'sjd.created_at as delivered_at','delivery_note_shipments.status as recovery_status','sps.name as payment_status','rider.name as rider_name', 's.booking_type_id', 'usi.poc','u.id as account_no')
            ->whereIn('delivery_note_shipments.status', [4,5,6,7,8]);
        if (session('role_id') != 1) {
            $shipments = $shipments->whereIn('dc.hub_id', session('hubs'));
        }
        $datatables = Datatables::of($shipments)
            ->setRowAttr([
                'data-dncc' => function ($shipments) {
                    return $shipments->dncc;
                },
                'data-sdn' => function ($shipments) {
                    return $shipments->sdn;
                },
            ])
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->editColumn('dncc', function ($shipments) {
                if ($shipments->dncc) {
                    return str_pad($shipments->dncc, 6, '0', STR_PAD_LEFT);
                }
                else {
                    return '';
                }
            })
            ->editColumn('account_no', function ($shipments) {
                    return str_pad($shipments->account_no, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper .' (' . $shipment->poc . ')';
                }
                else {
                    return $shipment->shipper;
                }
            })
            ->filterColumn('u.name', function ($query, $keyword) {
                $query->where(function ($sub_query) use ($keyword) {
                    $sub_query->where('shipments.booking_type_id', '!=', 4)
                        ->where('u.name', 'like', '%' . $keyword . '%');
                })
                    ->orWhere(function ($sub_query) use ($keyword) {
                        $sub_query->where('shipments.booking_type_id', '=', 4)
                            ->where('usi.poc', 'like', '%' . $keyword . '%');
                    });
            })
            ->editColumn('dncc_link', function($shipments) {
                return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($shipments->dncc, 6, '0', STR_PAD_LEFT) . '</span></button>';
            })
            ->editColumn('sdn_link', function($shipments) {
                return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($shipments->sdn, 6, '0', STR_PAD_LEFT) . '</span></button>';
            })
            ->editColumn('sdn', function ($shipments) {
                if ($shipments->sdn) {
                    return str_pad($shipments->sdn, 6, '0', STR_PAD_LEFT);
                }
                else {
                    return '';
                }
            })
            ->addColumn('aging', function($shipment) {
                $updated_at = Carbon::parse($shipment->operation_status_date)->startOfDay();

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
            $cities = DB::connection('reports')->table('cities')->select('id','name')->where('pickup',1)->get();
        }else{
            $cities = DB::connection('reports')->table('cities')->select('id','name')->where('pickup',1)->whereIn('hub_id',session('hubs'))->get();
        }
        $sales = '';
        if(session('department_id') == 7){
            $sales = DB::connection('reports')->table('admins')->whereExists(function($query) {
                $query->from('admin_roles')
                ->where('admins.role_id', '=', DB::raw('`admin_roles`.`id`'))
                ->where('department_id', '=', 7);
            })->select('id', 'name')->get();
        }
        return view('admin.reports.daily_pickup_sales_report')->with(['cities'=>$cities, 'sales_persons' => $sales]);
    }

    public function daily_pickup_sales_export_to_excel(Request $request){
        $response = self::daily_pickup_sales_report_create($request->city,$request->date,$request->sales_person,$request->sales_tagging);

//         if($response['status']){
        return $response;
//         }

    }
    function array_sort($array, $on, $order=SORT_DESC){

        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }
    static public function daily_pickup_sales_report_create($search_city = NULL, $date,$sales_person = NULL,$sales_tagging = FALSE){

        $date_from = Carbon::createFromFormat("Y-m-d H:i:s",$date)->format('Y-m-d 08:00A');
        $next_day = Carbon::parse($date)->addDay(1);
        $date_to = Carbon::createFromFormat("Y-m-d H:i:s",$next_day)->format('Y-m-d 07:59A');
        $only_date = Carbon::parse($date)->toDateString();
        $hubs = array();
        $city = array();
        if($sales_tagging == TRUE) {

            if (session('role_id') == 1) {
                if ($search_city != null) {

                    $city = DB::connection('reports')->table('cities')->where('id', $search_city)->select('id', 'name')->first();
                    $search_city_hub = $city->id;
                    $hubs[] = $city;
                } else {
                    $search_city_hub = '';
                    $hubs = DB::connection('reports')->table('cities')->where('pickup', 1)->select('id', 'name')->get();
                }
            } else {
                if ($search_city != null) {

                    $city = DB::connection('reports')->table('cities')->where('id', $search_city)->select('id', 'name')->first();
                    $search_city_hub = $city->id;
                    $hubs[] = $city;
                } else {
                    $search_city_hub = '';
                    $hubs = DB::connection('reports')->table('cities')->whereIn('hub_id', session('hubs'))->where('pickup', 1)->select('id', 'name')->get();
                }
            }
        }else{
            $search_city_hub = '';
            $hubs = DB::connection('reports')->table('cities')->where('pickup', 1)->select('id', 'name')->get();
        }
        $details = array();
        $sorted_details_array = array();
        $sorted_shipper_array = array();
        $details_shipper = array();
        $shippers = array();
//        $details['header'] = ['S. No.','Origin '.$only_date, 'No. of Parcels Booked','No of Parcels Received','Revenue without GST','Collection Amount','Actual Weight','Chargeable Weight','Avg/Parcel Revenue','Avg. Amount Collection','% Rev. on Amount Collection'];
        $details['header'] = ['S. No.','Origin '.$only_date, 'No. of Parcels Booked','No of Parcels Received','Revenue without GST','Avg/Parcel Revenue','Actual Weight','Avg. Actual Weight/Parcel','Avg. Revenue On Actual Weight','Chargeable Weight','Avg. Chargeable Weight/Parcel','Avg. Revenue On Chargeable Weight','Collection Amount','Avg. Amount Collection','% Rev. on Amount Collection'];
        $sort_support_array = array();
        $total_booked = 0;
        $total_received = 0;
        $total_cod_collection = 0;
        $total_actual_weight = 0;
        $total_avg_actual_weight = 0;
        $total_avg_rev_actual_weight = 0;
        $total_chargeable_weight = 0;
        $total_avg_chargeable_weight = 0;
        $total_avg_rev_chargeable_weight = 0;
        $total_revenue_wo_gst = 0;
        $total_avg_revenue = 0;
        $total_avg_cash_collection = 0;
        $total_rev_on_cash_collection = 0;
        $serial_number_hubs = 0;
        $booked = 0;
        $received = 0;
        $revenue_wo_gst = 0;
        $cod_collection = 0;
        $actual_weight = 0;
        $chargeable_weight = 0;
        foreach ($hubs as $hub) {
            if($sales_tagging == TRUE){

                if (session('department_id') != 7){
                    $booked = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                        $query->from('user_shipping_infos')
                        ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                        ->whereExists(function($sub_query) use ($hub) {
                            $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $hub->id);
                        });
                    })->whereBetween('shipments.created_at',[$date_from,$date_to])->where('shipments.packaging_material_request', '=', 0)->count();

                    $received = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                        $query->from('user_shipping_infos')
                        ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                        ->whereExists(function($sub_query) use ($hub) {
                            $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $hub->id);
                        });
                    })->whereExists(function ($query) use ($date_from, $date_to) {
                        $query->from('shipments_journey')
                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                        ->whereBetween('created_at', [$date_from, $date_to])
                        ->where('shipper_status_id', 2);
                    })->where('shipments.packaging_material_request', '=', 0)->count();
                    $cod_collection = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                        $query->from('user_shipping_infos')
                        ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                        ->whereExists(function($sub_query) use ($hub) {
                            $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $hub->id);
                        });
                    })->whereExists(function ($query) use ($date_from, $date_to) {
                        $query->from('shipments_journey')
                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                        ->whereBetween('created_at', [$date_from, $date_to])
                        ->where('shipper_status_id', 2);
                    })->where('shipments.packaging_material_request', '=', 0)->sum('amount');
                    $actual_weight = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                        $query->from('user_shipping_infos')
                        ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                        ->whereExists(function($sub_query) use ($hub) {
                            $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $hub->id);
                        });
                    })->whereExists(function ($query) use ($date_from, $date_to) {
                        $query->from('shipments_journey')
                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                        ->whereBetween('created_at', [$date_from, $date_to])
                        ->where('shipper_status_id', 2);
                    })->where('shipments.packaging_material_request', '=', 0)->sum('actual_weight');
                    $chargeable_weight = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                        $query->from('user_shipping_infos')
                        ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                        ->whereExists(function($sub_query) use ($hub) {
                            $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $hub->id);
                        });
                    })->whereExists(function ($query) use ($date_from, $date_to) {
                        $query->from('shipments_journey')
                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                        ->whereBetween('created_at', [$date_from, $date_to])
                        ->where('shipper_status_id', 2);
                    })->where('shipments.packaging_material_request', '=', 0)->sum('chargeable_weight');
                    $revenue_wo_gst = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                        $query->from('user_shipping_infos')
                        ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                        ->whereExists(function($sub_query) use ($hub) {
                            $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $hub->id);
                        });
                    })->whereExists(function ($query) use ($date_from, $date_to) {
                        $query->from('shipments_journey')
                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                        ->whereBetween('created_at', [$date_from, $date_to])
                        ->where('shipper_status_id', 2);
                    })->where('shipments.packaging_material_request', '=', 0)->sum(DB::connection('reports')->raw('IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)'));

                }else{
                    if(session('role_id') != 4){
                        $booked = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                            $query->from('user_shipping_infos')
                            ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                            ->whereExists(function($sub_query) use ($hub) {
                                $sub_query->from('cities')
                                ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                ->where('cities.id', $hub->id);
                            });
                        })->where('shipments.packaging_material_request', '=', 0)->whereBetween('created_at',[$date_from,$date_to])->whereIn('shipments.user_id', session('tagged_shippers'))->count();
                        $received = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                            $query->from('user_shipping_infos')
                            ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                            ->whereExists(function($sub_query) use ($hub) {
                                $sub_query->from('cities')
                                ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                ->where('cities.id', $hub->id);
                            });
                        })->whereExists(function ($query) use ($date_from, $date_to) {
                            $query->from('shipments_journey')
                            ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                            ->whereBetween('created_at', [$date_from, $date_to])
                            ->where('shipper_status_id', 2);
                        })->where('shipments.packaging_material_request', '=', 0)->whereIn('shipments.user_id', session('tagged_shippers'))->count();
                        $cod_collection = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                            $query->from('user_shipping_infos')
                            ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                            ->whereExists(function($sub_query) use ($hub) {
                                $sub_query->from('cities')
                                ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                ->where('cities.id', $hub->id);
                            });
                        })->whereExists(function ($query) use ($date_from, $date_to) {
                            $query->from('shipments_journey')
                            ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                            ->whereBetween('created_at', [$date_from, $date_to])
                            ->where('shipper_status_id', 2);
                        })->where('shipments.packaging_material_request', '=', 0)->whereIn('shipments.user_id', session('tagged_shippers'))->sum('amount');
                        $actual_weight = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                            $query->from('user_shipping_infos')
                            ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                            ->whereExists(function($sub_query) use ($hub) {
                                $sub_query->from('cities')
                                ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                ->where('cities.id', $hub->id);
                            });
                        })->whereExists(function ($query) use ($date_from, $date_to) {
                            $query->from('shipments_journey')
                            ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                            ->whereBetween('created_at', [$date_from, $date_to])
                            ->where('shipper_status_id', 2);
                        })->where('shipments.packaging_material_request', '=', 0)->whereIn('shipments.user_id', session('tagged_shippers'))->sum('actual_weight');
                        $chargeable_weight = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                            $query->from('user_shipping_infos')
                            ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                            ->whereExists(function($sub_query) use ($hub) {
                                $sub_query->from('cities')
                                ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                ->where('cities.id', $hub->id);
                            });
                        })->whereExists(function ($query) use ($date_from, $date_to) {
                            $query->from('shipments_journey')
                            ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                            ->whereBetween('created_at', [$date_from, $date_to])
                            ->where('shipper_status_id', 2);
                        })->where('shipments.packaging_material_request', '=', 0)->whereIn('shipments.user_id', session('tagged_shippers'))->sum('chargeable_weight');
                        $revenue_wo_gst = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                            $query->from('user_shipping_infos')
                            ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                            ->whereExists(function($sub_query) use ($hub) {
                                $sub_query->from('cities')
                                ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                ->where('cities.id', $hub->id);
                            });
                        })->whereExists(function ($query) use ($date_from, $date_to) {
                            $query->from('shipments_journey')
                            ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                            ->whereBetween('created_at', [$date_from, $date_to])
                            ->where('shipper_status_id', 2);
                        })->where('shipments.packaging_material_request', '=', 0)->whereIn('shipments.user_id', session('tagged_shippers'))->sum(DB::connection('reports')->raw('IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)'));
                    }else{
                        if($sales_person != null){
                            $tagged_shippers = DB::connection('reports')->table('sale_person_tags')->where('admin_id', $sales_person)->select('user_id')->get();

                            $booked = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                                $query->from('user_shipping_infos')
                                ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                ->whereExists(function($sub_query) use ($hub) {
                                    $sub_query->from('cities')
                                    ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                    ->where('cities.id', $hub->id);
                                });
                            })->where('shipments.packaging_material_request', '=', 0)->whereBetween('created_at',[$date_from,$date_to])->whereIn('user_id', $tagged_shippers)->count();

                            $received = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                                $query->from('user_shipping_infos')
                                ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                ->whereExists(function($sub_query) use ($hub) {
                                    $sub_query->from('cities')
                                    ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                    ->where('cities.id', $hub->id);
                                });
                            })->whereExists(function ($query) use ($date_from, $date_to) {
                                $query->from('shipments_journey')
                                ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                ->whereBetween('created_at', [$date_from, $date_to])
                                ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', '=', 0)->whereIn('user_id', $tagged_shippers)->count();
                            $cod_collection = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                                $query->from('user_shipping_infos')
                                ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                ->whereExists(function($sub_query) use ($hub) {
                                    $sub_query->from('cities')
                                    ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                    ->where('cities.id', $hub->id);
                                });
                            })->whereExists(function ($query) use ($date_from, $date_to) {
                                $query->from('shipments_journey')
                                ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                ->whereBetween('created_at', [$date_from, $date_to])
                                ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', '=', 0)->whereIn('user_id', $tagged_shippers)->sum('amount');
                            $actual_weight = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                                $query->from('user_shipping_infos')
                                ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                ->whereExists(function($sub_query) use ($hub) {
                                    $sub_query->from('cities')
                                    ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                    ->where('cities.id', $hub->id);
                                });
                            })->whereExists(function ($query) use ($date_from, $date_to) {
                                $query->from('shipments_journey')
                                ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                ->whereBetween('created_at', [$date_from, $date_to])
                                ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', '=', 0)->whereIn('user_id', $tagged_shippers)->sum('actual_weight');
                            $chargeable_weight = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                                $query->from('user_shipping_infos')
                                ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                ->whereExists(function($sub_query) use ($hub) {
                                    $sub_query->from('cities')
                                    ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                    ->where('cities.id', $hub->id);
                                });
                            })->whereExists(function ($query) use ($date_from, $date_to) {
                                $query->from('shipments_journey')
                                ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                ->whereBetween('created_at', [$date_from, $date_to])
                                ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', '=', 0)->whereIn('user_id', $tagged_shippers)->sum('chargeable_weight');
                            $revenue_wo_gst = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                                $query->from('user_shipping_infos')
                                ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                ->whereExists(function($sub_query) use ($hub) {
                                    $sub_query->from('cities')
                                    ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                    ->where('cities.id', $hub->id);
                                });
                            })->whereExists(function ($query) use ($date_from, $date_to) {
                                $query->from('shipments_journey')
                                ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                ->whereBetween('created_at', [$date_from, $date_to])
                                ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', '=', 0)->whereIn('user_id', $tagged_shippers)->sum(DB::connection('reports')->raw('IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)'));

                        }else{

                            $booked = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                                $query->from('user_shipping_infos')
                                ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                ->whereExists(function($sub_query) use ($hub) {
                                    $sub_query->from('cities')
                                    ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                    ->where('cities.id', $hub->id);
                                });
                            })->where('shipments.packaging_material_request', '=', 0)->whereBetween('created_at',[$date_from,$date_to])->count();
                            $received = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                                $query->from('user_shipping_infos')
                                ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                ->whereExists(function($sub_query) use ($hub) {
                                    $sub_query->from('cities')
                                    ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                    ->where('cities.id', $hub->id);
                                });
                            })->whereExists(function ($query) use ($date_from, $date_to) {
                                $query->from('shipments_journey')
                                ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                ->whereBetween('created_at', [$date_from, $date_to])
                                ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', '=', 0)->count();
                            $cod_collection = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                                $query->from('user_shipping_infos')
                                ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                ->whereExists(function($sub_query) use ($hub) {
                                    $sub_query->from('cities')
                                    ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                    ->where('cities.id', $hub->id);
                                });
                            })->whereExists(function ($query) use ($date_from, $date_to) {
                                $query->from('shipments_journey')
                                ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                ->whereBetween('created_at', [$date_from, $date_to])
                                ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', '=', 0)->sum('amount');
                            $actual_weight = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                                $query->from('user_shipping_infos')
                                ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                ->whereExists(function($sub_query) use ($hub) {
                                    $sub_query->from('cities')
                                    ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                    ->where('cities.id', $hub->id);
                                });
                            })->whereExists(function ($query) use ($date_from, $date_to) {
                                $query->from('shipments_journey')
                                ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                ->whereBetween('created_at', [$date_from, $date_to])
                                ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', '=', 0)->sum('actual_weight');
                            $chargeable_weight = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                                $query->from('user_shipping_infos')
                                ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                ->whereExists(function($sub_query) use ($hub) {
                                    $sub_query->from('cities')
                                    ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                    ->where('cities.id', $hub->id);
                                });
                            })->whereExists(function ($query) use ($date_from, $date_to) {
                                $query->from('shipments_journey')
                                ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                ->whereBetween('created_at', [$date_from, $date_to])
                                ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', '=', 0)->sum('chargeable_weight');
                            $revenue_wo_gst = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                                $query->from('user_shipping_infos')
                                ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                ->whereExists(function($sub_query) use ($hub) {
                                    $sub_query->from('cities')
                                    ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                    ->where('cities.id', $hub->id);
                                });
                            })->whereExists(function ($query) use ($date_from, $date_to) {
                                $query->from('shipments_journey')
                                ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                ->whereBetween('created_at', [$date_from, $date_to])
                                ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', '=', 0)->sum(DB::connection('reports')->raw('IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)'));

                        }
                    }
                }

            }else{

                $booked = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                    $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function($sub_query) use ($hub) {
                        $sub_query->from('cities')
                        ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                        ->where('cities.id', $hub->id);
                    });
                })->where('shipments.packaging_material_request', '=', 0)->whereBetween('created_at',[$date_from,$date_to])->count();
                $received = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                    $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function($sub_query) use ($hub) {
                        $sub_query->from('cities')
                        ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                        ->where('cities.id', $hub->id);
                    });
                })->whereExists(function ($query) use ($date_from, $date_to) {
                    $query->from('shipments_journey')
                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                    ->whereBetween('created_at', [$date_from, $date_to])
                    ->where('shipper_status_id', 2);
                })->where('shipments.packaging_material_request', '=', 0)->count();
                $cod_collection = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                    $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function($sub_query) use ($hub) {
                        $sub_query->from('cities')
                        ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                        ->where('cities.id', $hub->id);
                    });
                })->whereExists(function ($query) use ($date_from, $date_to) {
                    $query->from('shipments_journey')
                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                    ->whereBetween('created_at', [$date_from, $date_to])
                    ->where('shipper_status_id', 2);
                })->where('shipments.packaging_material_request', '=', 0)->sum('amount');
                $actual_weight = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                    $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function($sub_query) use ($hub) {
                        $sub_query->from('cities')
                        ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                        ->where('cities.id', $hub->id);
                    });
                })->whereExists(function ($query) use ($date_from, $date_to) {
                    $query->from('shipments_journey')
                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                    ->whereBetween('created_at', [$date_from, $date_to])
                    ->where('shipper_status_id', 2);
                })->where('shipments.packaging_material_request', '=', 0)->sum('actual_weight');
                $chargeable_weight = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                    $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function($sub_query) use ($hub) {
                        $sub_query->from('cities')
                        ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                        ->where('cities.id', $hub->id);
                    });
                })->whereExists(function ($query) use ($date_from, $date_to) {
                    $query->from('shipments_journey')
                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                    ->whereBetween('created_at', [$date_from, $date_to])
                    ->where('shipper_status_id', 2);
                })->where('shipments.packaging_material_request', '=', 0)->sum('chargeable_weight');
                $revenue_wo_gst = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($hub) {
                    $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function($sub_query) use ($hub) {
                        $sub_query->from('cities')
                        ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                        ->where('cities.id', $hub->id);
                    });
                })->whereExists(function ($query) use ($date_from, $date_to) {
                    $query->from('shipments_journey')
                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                    ->whereBetween('created_at', [$date_from, $date_to])
                    ->where('shipper_status_id', 2);
                })->where('shipments.packaging_material_request', '=', 0)->sum(DB::connection('reports')->raw('IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)'));
            }
            if($booked > 0 || $received > 0){

                $row = array();
                $avg_revenue = ($received != 0)? $revenue_wo_gst/$received:0;
                $avg_cash_collection = ($received != 0)? $cod_collection/$received:0;
                $rev_on_cash_collection = (($avg_cash_collection != 0)? $avg_revenue/$avg_cash_collection:0)*100;
                //changes add columns
                $avg_actual_weight = ($received != 0)? $actual_weight/$received:0;
                $avg_rev_actual_weight = ($actual_weight != 0 && $revenue_wo_gst != 0)? $revenue_wo_gst/$actual_weight:0;
                $avg_chargeable_weight = ($received != 0)? $chargeable_weight/$received:0;
                $avg_rev_chargeable_weight = ($chargeable_weight != 0 && $revenue_wo_gst != 0)? $revenue_wo_gst/$chargeable_weight:0;

                //end changes
                $row['serials'] = $serial_number_hubs;
                $row['hub'] = $hub->name;
                $row['booked'] = number_format($booked);
                $row['received'] = number_format($received);
                $row['revenue_wo_gst'] = number_format($revenue_wo_gst);
                $avg_rev = round($avg_revenue);
                $row['average_revenue'] = number_format($avg_rev);
                $row['actual_weight'] =number_format($actual_weight);
                $string_avg_actual_weight = (string) $avg_actual_weight;
                $row['avg_actual_weight'] = number_format((float)$string_avg_actual_weight,2,'.','');
                $row['avg_rev_actual_weight'] = round($avg_rev_actual_weight);

                $row['chargeable_weight'] = number_format($chargeable_weight);
                $string_avg_chargeable_weight = (string) $avg_chargeable_weight;
                $row['avg_chargeable_weight'] = number_format((float)$string_avg_chargeable_weight,2,'.','');
                $row['avg_rev_chargeable_weight'] = round($avg_rev_chargeable_weight);
                $row['cod_collection'] = number_format($cod_collection);

                $avg_cc = round($avg_cash_collection);
                $row['average_cash_collection'] = number_format($avg_cc);
                $rev_occ =   round($rev_on_cash_collection);
                $row['revenue_cash_collection'] = number_format($rev_occ).'%';
                $sort_support_array[] = $received;

                $sorted_details_array[] = $row;
                $total_booked += $booked;
                $total_received += $received;
                $total_revenue_wo_gst += $revenue_wo_gst;
                $total_cod_collection += $cod_collection;
                $total_actual_weight += $actual_weight;
//                $total_avg_actual_weight += $avg_actual_weight;
//                $total_avg_rev_actual_weight += $avg_rev_actual_weight;
                $total_chargeable_weight += $chargeable_weight;
//                $total_avg_chargeable_weight += $avg_chargeable_weight;
//                $total_avg_rev_chargeable_weight += $avg_rev_chargeable_weight;
//                $total_avg_revenue += $avg_rev;

//                $total_rev_on_cash_collection += $rev_occ;

            }

        }
        $total_avg_revenue = ($total_received != 0) ? $total_revenue_wo_gst / $total_received:0;
        $total_avg_actual_weight = ($total_received != 0) ? $total_actual_weight / $total_received:0;
        $total_avg_rev_actual_weight = ($total_actual_weight != 0) ? $total_revenue_wo_gst / $total_actual_weight:0;
        $total_avg_chargeable_weight = ($total_received != 0) ? $total_chargeable_weight / $total_received:0;
        $total_avg_rev_chargeable_weight = ($total_chargeable_weight != 0) ? $total_revenue_wo_gst / $total_chargeable_weight:0;
        $total_avg_cash_collection = ($total_received != 0) ? $total_cod_collection / $total_received:0;
        $total_rev_on_cash_collection = ($total_cod_collection != 0) ? $total_revenue_wo_gst / $total_cod_collection:0;
        $total_rev_on_cash_collection = $total_rev_on_cash_collection * 100;
        array_multisort($sort_support_array, SORT_DESC, $sorted_details_array);
        $counter = 1;
        foreach ($sorted_details_array as $item) {
            $item['serials'] = $counter;
            $details[] = $item;
            $counter++;
        }
        $details[] = ['Grand Total','Origin '.$only_date, number_format($total_booked), number_format($total_received), number_format($total_revenue_wo_gst),number_format($total_avg_revenue),number_format($total_actual_weight),number_format((float) $total_avg_actual_weight,2,'.',''),round($total_avg_rev_actual_weight),number_format($total_chargeable_weight),number_format((float) $total_avg_chargeable_weight,2,'.',''), round($total_avg_rev_chargeable_weight), number_format($total_cod_collection),number_format($total_avg_cash_collection),number_format($total_rev_on_cash_collection).'%'];

        $details_shipper['header'] = ['S. No.','Origin','Sales Person','Shipper Name(s) (Account No(s))', 'No. of Parcels Booked','No of Parcels Received','Revenue without GST','Avg/Parcel Revenue','Actual Weight','Avg. Actual Weight/Parcel','Avg. Revenue On Actual Weight','Chargeable Weight','Avg. Chargeable Weight/Parcel','Avg. Revenue On Chargeable Weight','Collection Amount','Avg. Amount Collection','% Rev. on Amount Collection'];

//        $details_shipper['header'] = ['S. No.','DSR '.$only_date, 'No. of Parcels Booked','No of Parcels Received','Revenue without GST','Collection Amount','Actual Weight','Chargeable Weight','Avg/Parcel Revenue','Avg. Amount Collection','% Rev. on Amount Collection'];
        $serial_number_shippers = 0;

        if($sales_tagging == TRUE){
            if($search_city != null){
                $pickup_request_shippers = DB::connection('reports')->table('shipments')->whereExists(function($query) use ($search_city_hub) {
                    $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function($sub_query) use ($search_city_hub) {
                        $sub_query->from('cities')
                        ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                        ->where('cities.id', $search_city_hub);
                    });
                })->whereExists(function ($query) use ($date_from, $date_to) {
                    $query->from('shipments_journey')
                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                    ->whereBetween('created_at', [$date_from, $date_to]);
                });

                if($pickup_request_shippers->exists()){
                    $pickup_request_shippers_ids = $pickup_request_shippers->pluck('user_id')->toArray();
                    $pickup_request_shippers_ids = array_unique($pickup_request_shippers_ids);

                    if(session('department_id') != 7){
                        $shippers[$search_city_hub] = DB::connection('reports')->table('users')->select('id','name')->whereIn('id',$pickup_request_shippers_ids)->whereIn('status',[3, 4])->get();
                    }else{
                        if(session('role_id') != 4){
                            $shippers[$search_city_hub] = DB::connection('reports')->table('users')->select('id','name')->whereIn('id',$pickup_request_shippers_ids)->whereIn('status',[3, 4])->whereIn('id', session('tagged_shippers'))->get();
                        }else{
                            $shippers[$search_city_hub] = DB::connection('reports')->table('users')->select('id','name')->whereIn('id',$pickup_request_shippers_ids)->whereIn('status',[3, 4])->get();
                        }
                    }

                }
            }else{
                foreach($hubs as $hub){

                        $pickup_request_shippers = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($hub) {
                            $query->from('user_shipping_infos')
                                ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                ->whereExists(function ($sub_query) use ($hub) {
                                    $sub_query->from('cities')
                                        ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                        ->where('cities.id', $hub->id);
                                });
                        })->whereExists(function ($query) use ($date_from, $date_to) {
                            $query->from('shipments_journey')
                                ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                ->whereBetween('created_at', [$date_from, $date_to]);
                        });

                   if($pickup_request_shippers->exists()) {
                       $pickup_request_shippers_ids = $pickup_request_shippers->pluck('user_id')->toArray();
                        
                       $pickup_request_shippers_ids = array_unique($pickup_request_shippers_ids);

                       if (session('department_id') != 7) {


                           $shippers[$hub->id] = DB::connection('reports')->table('users')->select('id', 'name')->whereIn('id', $pickup_request_shippers_ids)->whereIn('status', [3, 4])->get();
//                        }
                       } else {
                           if (session('role_id') != 4) {

                               $shippers[$hub->id] = DB::connection('reports')->table('users')->select('id', 'name')->whereIn('id', $pickup_request_shippers_ids)->whereIn('status', [3, 4])->whereIn('id', session('tagged_shippers'))->get();
//                            }


                           } else {
                               if ($sales_person != null) {

                                   $shippers[$hub->id] = DB::connection('reports')->table('users')->whereExists(function ($query) use ($sales_person) {
                                       $query->from('sale_person_tags')
                                           ->where('users.id', '=', DB::raw('`sale_person_tags`.`user_id`'))
                                           ->where('admin_id', '=', $sales_person);
                                   })->whereIn('id', $pickup_request_shippers_ids)->whereIn('status', [3, 4])->select('id', 'name')->get();
//                                }

                               } else {
                                   $shippers[$hub->id] = DB::connection('reports')->table('users')->select('id', 'name')->whereIn('id', $pickup_request_shippers_ids)->whereIn('status', [3, 4])->get();

                               }

                           }
                       }
                   }

                }

            }
        }
        else{
            foreach($hubs as $hub){

                $pickup_request_shippers = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($hub) {
                    $query->from('user_shipping_infos')
                        ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                        ->whereExists(function ($sub_query) use ($hub) {
                            $sub_query->from('cities')
                                ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                ->where('cities.id', $hub->id);
                        });
                })->whereExists(function ($query) use ($date_from, $date_to) {
                    $query->from('shipments_journey')
                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                        ->whereBetween('created_at', [$date_from, $date_to]);
                });

                if($pickup_request_shippers->exists()) {
                    $pickup_request_shippers_ids = $pickup_request_shippers->pluck('user_id')->toArray();

                    $pickup_request_shippers_ids = array_unique($pickup_request_shippers_ids);

                    $shippers[$hub->id] = DB::connection('reports')->table('users')->select('id', 'name')->whereIn('id', $pickup_request_shippers_ids)->whereIn('status', [3, 4])->get();
                }

            }
        }
        
        $shipper_sort_support_array = array();
        $total_shipper_booked = 0;
        $total_shipper_received = 0;
        $total_shipper_cod_collection = 0;
        $total_shipper_actual_weight = 0;
        $total_shipper_avg_actual_weight = 0;
        $total_shipper_avg_rev_actual_weight = 0;
        $total_shipper_chargeable_weight = 0;
        $total_shipper_avg_chargeable_weight = 0;
        $total_shipper_avg_rev_chargeable_weight = 0;
        $total_shipper_revenue_wo_gst = 0;
        $total_shipper_avg_revenue = 0;
        $total_shipper_avg_cash_collection = 0;
        $total_shipper_rev_on_cash_collection = 0;
//        $shipper_sales_person = '';



        if(count($shippers) > 0) {
            if($search_city != null){
                $origin_name =  DB::connection('reports')->table('cities')->where('id', $search_city_hub)->select('name')->first();
                $origin_name = $origin_name->name;

            }
            foreach ($shippers as $origin => $shipper_row) {
                    foreach ($shipper_row as $shipper) {

                        $shipper_booked = 0;
                        $shipper_received = 0;
                        $shipper_rev_wo_gst = 0;
                        $shipper_cod = 0;
                        $shipper_actual_weight = 0;
                        $shipper_chargeable_weight = 0;
                        if($search_city == null){
                            $origin_name =  DB::connection('reports')->table('cities')->where('id', $origin)->select('name')->first();
                            $origin_name = $origin_name->name;
                        }

                        $shipper_sales_person_name = '';
                        $shipper_sales_person = SalePersonTag::where('user_id', $shipper->id)->where('status', 0);
                        if ($shipper_sales_person->exists()) {
                            $shipper_sales_person = $shipper_sales_person->first();
                            $shipper_sales_person_name = Admin::find($shipper_sales_person->admin_id)->name;
                        }
                        if ($sales_tagging == TRUE) {
                            if ($search_city != null) {
                                $shipper_booked = DB::connection('reports')->table('shipments')->where('user_id', $shipper->id)
                                    ->whereExists(function ($query) use ($hubs) {
                                        $query->from('user_shipping_infos')
                                            ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                            ->whereExists(function ($sub_query) use ($hubs) {
                                                $sub_query->from('cities')
                                                    ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                                    ->where('cities.id', $hubs[0]->id);
                                            });
                                    })->where('shipments.packaging_material_request', '=', 0)->whereBetween('created_at', [$date_from, $date_to])->count();
                                $shipper_received = DB::connection('reports')->table('shipments')->where('user_id', $shipper->id)
                                    ->whereExists(function ($query) use ($hubs) {
                                        $query->from('user_shipping_infos')
                                            ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                            ->whereExists(function ($sub_query) use ($hubs) {
                                                $sub_query->from('cities')
                                                    ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                                    ->where('cities.id', $hubs[0]->id);
                                            });
                                    })
                                    ->whereExists(function ($query) use ($date_from, $date_to) {
                                        $query->from('shipments_journey')
                                            ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                            ->whereBetween('created_at', [$date_from, $date_to])
                                            ->where('shipper_status_id', 2);
                                    })->where('shipments.packaging_material_request', '=', 0)->count();
                                $shipper_rev_wo_gst = DB::connection('reports')->table('shipments')->where('user_id', $shipper->id)
                                    ->whereExists(function ($query) use ($hubs) {
                                        $query->from('user_shipping_infos')
                                            ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                            ->whereExists(function ($sub_query) use ($hubs) {
                                                $sub_query->from('cities')
                                                    ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                                    ->where('cities.id', $hubs[0]->id);
                                            });
                                    })
                                    ->whereExists(function ($query) use ($date_from, $date_to) {
                                        $query->from('shipments_journey')
                                            ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                            ->whereBetween('created_at', [$date_from, $date_to])
                                            ->where('shipper_status_id', 2);
                                    })->where('shipments.packaging_material_request', '=', 0)->sum(DB::connection('reports')->raw('IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)'));
                                $shipper_cod = DB::connection('reports')->table('shipments')->where('user_id', $shipper->id)
                                    ->whereExists(function ($query) use ($hubs) {
                                        $query->from('user_shipping_infos')
                                            ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                            ->whereExists(function ($sub_query) use ($hubs) {
                                                $sub_query->from('cities')
                                                    ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                                    ->where('cities.id', $hubs[0]->id);
                                            });
                                    })
                                    ->whereExists(function ($query) use ($date_from, $date_to) {
                                        $query->from('shipments_journey')
                                            ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                            ->whereBetween('created_at', [$date_from, $date_to])
                                            ->where('shipper_status_id', 2);
                                    })->where('shipments.packaging_material_request', '=', 0)->sum('amount');
                                $shipper_actual_weight = DB::connection('reports')->table('shipments')->where('user_id', $shipper->id)
                                    ->whereExists(function ($query) use ($hubs) {
                                        $query->from('user_shipping_infos')
                                            ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                            ->whereExists(function ($sub_query) use ($hubs) {
                                                $sub_query->from('cities')
                                                    ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                                    ->where('cities.id', $hubs[0]->id);
                                            });
                                    })
                                    ->whereExists(function ($query) use ($date_from, $date_to) {
                                        $query->from('shipments_journey')
                                            ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                            ->whereBetween('created_at', [$date_from, $date_to])
                                            ->where('shipper_status_id', 2);
                                    })->where('shipments.packaging_material_request', '=', 0)->sum('actual_weight');
                                $shipper_chargeable_weight = DB::connection('reports')->table('shipments')->where('user_id', $shipper->id)
                                    ->whereExists(function ($query) use ($hubs) {
                                        $query->from('user_shipping_infos')
                                            ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                            ->whereExists(function ($sub_query) use ($hubs) {
                                                $sub_query->from('cities')
                                                    ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                                    ->where('cities.id', $hubs[0]->id);
                                            });
                                    })
                                    ->whereExists(function ($query) use ($date_from, $date_to) {
                                        $query->from('shipments_journey')
                                            ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                            ->whereBetween('created_at', [$date_from, $date_to])
                                            ->where('shipper_status_id', 2);
                                    })->where('shipments.packaging_material_request', '=', 0)->sum('chargeable_weight');

                            } else {
                                $shipper_booked = DB::connection('reports')->table('shipments')->where('user_id', $shipper->id)
                                    ->whereExists(function ($query) use ($origin) {
                                        $query->from('user_shipping_infos')
                                            ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                            ->whereExists(function ($sub_query) use ($origin) {
                                                $sub_query->from('cities')
                                                    ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                                    ->where('cities.id',$origin);
                                            });
                                    })->whereBetween('created_at', [$date_from, $date_to])->where('shipments.packaging_material_request', '=', 0)->count();
                                $shipper_received = DB::connection('reports')->table('shipments')->where('user_id', $shipper->id)
                                    ->whereExists(function ($query) use ($origin) {
                                        $query->from('user_shipping_infos')
                                            ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                            ->whereExists(function ($sub_query) use ($origin) {
                                                $sub_query->from('cities')
                                                    ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                                    ->where('cities.id',$origin);
                                            });
                                    })->whereExists(function ($query) use ($date_from, $date_to) {
                                    $query->from('shipments_journey')
                                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                        ->whereBetween('created_at', [$date_from, $date_to])
                                        ->where('shipper_status_id', 2);
                                })->where('shipments.packaging_material_request', '=', 0)->count();
                                $shipper_rev_wo_gst = DB::connection('reports')->table('shipments')->where('user_id', $shipper->id)
                                    ->whereExists(function ($query) use ($origin) {
                                        $query->from('user_shipping_infos')
                                            ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                            ->whereExists(function ($sub_query) use ($origin) {
                                                $sub_query->from('cities')
                                                    ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                                    ->where('cities.id',$origin);
                                            });
                                    })->whereExists(function ($query) use ($date_from, $date_to) {
                                    $query->from('shipments_journey')
                                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                        ->whereBetween('created_at', [$date_from, $date_to])
                                        ->where('shipper_status_id', 2);
                                })->where('shipments.packaging_material_request', '=', 0)->sum(DB::connection('reports')->raw('IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)'));
                                $shipper_cod = DB::connection('reports')->table('shipments')->where('user_id', $shipper->id)
                                    ->whereExists(function ($query) use ($origin) {
                                        $query->from('user_shipping_infos')
                                            ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                            ->whereExists(function ($sub_query) use ($origin) {
                                                $sub_query->from('cities')
                                                    ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                                    ->where('cities.id',$origin);
                                            });
                                    })->whereExists(function ($query) use ($date_from, $date_to) {
                                    $query->from('shipments_journey')
                                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                        ->whereBetween('created_at', [$date_from, $date_to])
                                        ->where('shipper_status_id', 2);
                                })->where('shipments.packaging_material_request', '=', 0)->sum('amount');
                                $shipper_actual_weight = DB::connection('reports')->table('shipments')->where('user_id', $shipper->id)
                                    ->whereExists(function ($query) use ($origin) {
                                        $query->from('user_shipping_infos')
                                            ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                            ->whereExists(function ($sub_query) use ($origin) {
                                                $sub_query->from('cities')
                                                    ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                                    ->where('cities.id',$origin);
                                            });
                                    })->whereExists(function ($query) use ($date_from, $date_to) {
                                    $query->from('shipments_journey')
                                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                        ->whereBetween('created_at', [$date_from, $date_to])
                                        ->where('shipper_status_id', 2);
                                })->where('shipments.packaging_material_request', '=', 0)->sum('actual_weight');
                                $shipper_chargeable_weight = DB::connection('reports')->table('shipments')->where('user_id', $shipper->id)
                                    ->whereExists(function ($query) use ($origin) {
                                        $query->from('user_shipping_infos')
                                            ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                            ->whereExists(function ($sub_query) use ($origin) {
                                                $sub_query->from('cities')
                                                    ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                                    ->where('cities.id',$origin);
                                            });
                                    })->whereExists(function ($query) use ($date_from, $date_to) {
                                    $query->from('shipments_journey')
                                        ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                        ->whereBetween('created_at', [$date_from, $date_to])
                                        ->where('shipper_status_id', 2);
                                })->where('shipments.packaging_material_request', '=', 0)->sum('chargeable_weight');

                            }

                        } else {
                            $shipper_booked = DB::connection('reports')->table('shipments')->where('user_id', $shipper->id)
                                ->whereExists(function ($query) use ($origin) {
                                    $query->from('user_shipping_infos')
                                        ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                        ->whereExists(function ($sub_query) use ($origin) {
                                            $sub_query->from('cities')
                                                ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                                ->where('cities.id',$origin);
                                        });
                                })->whereBetween('created_at', [$date_from, $date_to])->where('shipments.packaging_material_request', '=', 0)->count();
                            $shipper_received = DB::connection('reports')->table('shipments')->where('user_id', $shipper->id)
                                ->whereExists(function ($query) use ($origin) {
                                    $query->from('user_shipping_infos')
                                        ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                        ->whereExists(function ($sub_query) use ($origin) {
                                            $sub_query->from('cities')
                                                ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                                ->where('cities.id',$origin);
                                        });
                                })->whereExists(function ($query) use ($date_from, $date_to) {
                                $query->from('shipments_journey')
                                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                    ->whereBetween('created_at', [$date_from, $date_to])
                                    ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', '=', 0)->count();
                            $shipper_rev_wo_gst = DB::connection('reports')->table('shipments')->where('user_id', $shipper->id)
                                ->whereExists(function ($query) use ($origin) {
                                    $query->from('user_shipping_infos')
                                        ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                        ->whereExists(function ($sub_query) use ($origin) {
                                            $sub_query->from('cities')
                                                ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                                ->where('cities.id',$origin);
                                        });
                                })->whereExists(function ($query) use ($date_from, $date_to) {
                                $query->from('shipments_journey')
                                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                    ->whereBetween('created_at', [$date_from, $date_to])
                                    ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', '=', 0)->sum(DB::connection('reports')->raw('IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)'));
                            $shipper_cod = DB::connection('reports')->table('shipments')->where('user_id', $shipper->id)
                                ->whereExists(function ($query) use ($origin) {
                                    $query->from('user_shipping_infos')
                                        ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                        ->whereExists(function ($sub_query) use ($origin) {
                                            $sub_query->from('cities')
                                                ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                                ->where('cities.id',$origin);
                                        });
                                })->whereExists(function ($query) use ($date_from, $date_to) {
                                $query->from('shipments_journey')
                                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                    ->whereBetween('created_at', [$date_from, $date_to])
                                    ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', '=', 0)->sum('amount');
                            $shipper_actual_weight = DB::connection('reports')->table('shipments')->where('user_id', $shipper->id)
                                ->whereExists(function ($query) use ($origin) {
                                    $query->from('user_shipping_infos')
                                        ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                        ->whereExists(function ($sub_query) use ($origin) {
                                            $sub_query->from('cities')
                                                ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                                ->where('cities.id',$origin);
                                        });
                                })->whereExists(function ($query) use ($date_from, $date_to) {
                                $query->from('shipments_journey')
                                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                    ->whereBetween('created_at', [$date_from, $date_to])
                                    ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', '=', 0)->sum('actual_weight');
                            $shipper_chargeable_weight = DB::connection('reports')->table('shipments')->where('user_id', $shipper->id)
                                ->whereExists(function ($query) use ($origin) {
                                    $query->from('user_shipping_infos')
                                        ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                        ->whereExists(function ($sub_query) use ($origin) {
                                            $sub_query->from('cities')
                                                ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                                ->where('cities.id',$origin);
                                        });
                                })->whereExists(function ($query) use ($date_from, $date_to) {
                                $query->from('shipments_journey')
                                    ->where('shipments.id', DB::raw('`shipments_journey`.`shipment_id`'))
                                    ->whereBetween('created_at', [$date_from, $date_to])
                                    ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', '=', 0)->sum('chargeable_weight');
                        }

                        $shipper_avg_revenue = ($shipper_received != 0) ? $shipper_rev_wo_gst / $shipper_received : 0;
                        $shipper_avg_cash_collection = ($shipper_received != 0) ? $shipper_cod / $shipper_received : 0;
                        $shipper_rev_on_cash_collection = (($shipper_avg_cash_collection != 0) ? $shipper_avg_revenue / $shipper_avg_cash_collection : 0) * 100;

                        //changes add columns
                        $shipper_avg_actual_weight = ($shipper_received != 0) ? $shipper_actual_weight / $shipper_received : 0;
                        $shipper_avg_rev_actual_weight = ($shipper_actual_weight != 0 && $shipper_rev_wo_gst != 0) ? $shipper_rev_wo_gst / $shipper_actual_weight : 0;
                        $shipper_avg_chargeable_weight = ($shipper_received != 0) ? $shipper_chargeable_weight / $shipper_received : 0;
                        $shipper_avg_rev_chargeable_weight = ($shipper_chargeable_weight != 0 && $shipper_rev_wo_gst != 0) ? $shipper_rev_wo_gst / $shipper_chargeable_weight : 0;

                        //end
                        $shipper_row = array();
                        $shipper_row['shipper_serial'] = $serial_number_shippers;
                        $shipper_row['origin_name'] = $origin_name;
                        $shipper_row['shipper_sale_person'] = $shipper_sales_person_name;
                        $shipper_row['name'] = $shipper->name . ' (' . str_pad($shipper->id, 6, '0', STR_PAD_LEFT) . ')';
                        $shipper_row['shipper_booked'] = number_format($shipper_booked);
                        $shipper_row['shipper_received'] = number_format($shipper_received);
                        $shipper_row['shipper_rev_wo_gst'] = number_format($shipper_rev_wo_gst);
                        $s_avg_revenue = round($shipper_avg_revenue);
                        $shipper_row['shipper_avg_revenue'] = number_format($s_avg_revenue);
                        $shipper_row['shipper_actual_weight'] = number_format($shipper_actual_weight);
                        $shipper_row['shipper_avg_actual_weight'] = number_format((float)$shipper_avg_actual_weight, 2, '.', '');
                        $shipper_row['$shipper_avg_rev_actual_weight'] = round($shipper_avg_rev_actual_weight);
                        $shipper_row['shipper_chargeable_weight'] = number_format($shipper_chargeable_weight);
                        $shipper_row['shipper_avg_chargeable_weight'] = number_format((float)$shipper_avg_chargeable_weight, 2, '.', '');
                        $shipper_row['shipper_avg_rev_chargeable_weight'] = round($shipper_avg_rev_chargeable_weight);
                        $shipper_row['shipper_cod'] = number_format($shipper_cod);

                        $avg_cash_coll = round($shipper_avg_cash_collection);
                        $shipper_row['shipper_avg_cc'] = number_format($avg_cash_coll);
                        $avg_rev_cc = round($shipper_rev_on_cash_collection);
                        $shipper_row['shipper_rcc'] = number_format($avg_rev_cc) . '%';

                        $sorted_shipper_array[] = $shipper_row;
                        $shipper_sort_support_array[] = $shipper_received;
                        $total_shipper_booked += $shipper_booked;
                        $total_shipper_received += $shipper_received;
                        $total_shipper_cod_collection += $shipper_cod;
                        $total_shipper_revenue_wo_gst += $shipper_rev_wo_gst;
                        $total_shipper_actual_weight += $shipper_actual_weight;
//                $total_shipper_avg_actual_weight += $shipper_avg_actual_weight;
//                $total_shipper_avg_rev_actual_weight += $shipper_avg_rev_actual_weight;
                        $total_shipper_chargeable_weight += $shipper_chargeable_weight;
//                $total_shipper_avg_chargeable_weight += $shipper_avg_chargeable_weight;
//                $total_shipper_avg_rev_chargeable_weight += $shipper_avg_rev_chargeable_weight;
//                $total_shipper_avg_revenue += $s_avg_revenue;

//                $total_shipper_rev_on_cash_collection += $avg_rev_cc;


//                $serial_number_shippers++;
                }
            }
        }

        $total_shipper_avg_revenue = ($total_shipper_received != 0) ? $total_shipper_revenue_wo_gst / $total_shipper_received:0;
        $total_shipper_avg_actual_weight = ($total_shipper_received != 0) ? $total_shipper_actual_weight / $total_shipper_received:0;
        $total_shipper_avg_rev_actual_weight = ($total_shipper_actual_weight != 0) ? $total_shipper_revenue_wo_gst / $total_shipper_actual_weight:0;
        $total_shipper_avg_chargeable_weight = ($total_shipper_received != 0) ? $total_shipper_chargeable_weight / $total_shipper_received:0;
        $total_shipper_avg_rev_chargeable_weight = ($total_shipper_chargeable_weight != 0) ? $total_shipper_revenue_wo_gst / $total_shipper_chargeable_weight:0;
        $total_shipper_avg_cash_collection = ($total_shipper_received != 0) ? $total_shipper_cod_collection / $total_shipper_received:0;

        $total_shipper_rev_on_cash_collection = ($total_shipper_cod_collection != 0) ? $total_shipper_revenue_wo_gst / $total_shipper_cod_collection:0;
        $total_shipper_rev_on_cash_collection = $total_shipper_rev_on_cash_collection * 100;
        array_multisort($shipper_sort_support_array, SORT_DESC, $sorted_shipper_array);
        $shipper_counter = 1;
        foreach ($sorted_shipper_array as $shipper) {
            $shipper['shipper_serial'] = $shipper_counter;
            $details_shipper[] = $shipper;
            $shipper_counter++;
        }
        $details_shipper[] = ['Grand Total','','','DSR '.$only_date, number_format($total_shipper_booked), number_format($total_shipper_received), number_format($total_shipper_revenue_wo_gst),number_format($total_shipper_avg_revenue),number_format($total_shipper_actual_weight),number_format((float) $total_shipper_avg_actual_weight,2,'.',''),round($total_shipper_avg_rev_actual_weight),number_format($total_shipper_chargeable_weight),number_format((float) $total_shipper_avg_chargeable_weight,2,'.',''),round($total_shipper_avg_rev_chargeable_weight), number_format($total_shipper_cod_collection),number_format($total_shipper_avg_cash_collection),number_format($total_shipper_rev_on_cash_collection).'%'];

        $spreadsheet = new Spreadsheet();
        $cell_st =[
            'font' =>['bold' => true],
            'alignment' =>['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                )
            ),
        ];
        $total_cell_st =[
            'font' =>['bold' => true],
            'borders'=>['bottom' =>['style'=> \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]],
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                ),
            )
        ];
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getDefaultColumnDimension()->setWidth(20);
        $sheet->getStyle('D3:R3')
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('CECECE');
        $sheet->getStyle('D3:R3')->getAlignment()->setWrapText(true);
        $sheet->fromArray($details,NULL,'D3',true);
        $count_hubs = count($hubs);
        $count_hub_rows = count($details);
        $count_hub_rows += 2;


        $count_hubs += 3;
        $total_shipper_rows = count($details_shipper);
        $total_shipper_rows += $count_hubs;
        $total_shipper_rows = $total_shipper_rows - 1;

        $shipper_cell = 'D'.$count_hubs;
        $shipper_last_cell = 'T'.$count_hubs;
        $sheet->fromArray($details_shipper,NULL,$shipper_cell,true);

        $sheet->setTitle('Daily Pickup Sales Report');

        $hub_all_rows = "D3".":R".$count_hub_rows;

        $sheet->getStyle($hub_all_rows)->applyFromArray($cell_st);

        $shipper_style_cell = "D$count_hubs".":T".$count_hubs;
        $shipper_all_rows = "D$count_hubs".":T".$total_shipper_rows;

        $total_style_cell = "D$count_hub_rows".":R".$count_hub_rows;
        $total_shipper_style_cell = "D$total_shipper_rows".":T".$total_shipper_rows;
        $sheet->getStyle($shipper_style_cell)
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('CECECE');
//        $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
        $sheet->getStyle($shipper_all_rows)->applyFromArray($cell_st);

        $sheet->getStyle($shipper_style_cell)->getAlignment()->setWrapText(true);
//        $sheet->getStyle($total_shipper_style_cell)->applyFromArray($total_cell_st);

        $set_shipper_actual_number_format = 'K'.$count_hubs.':K'.$total_shipper_rows;
        $set_shipper_chargeable_number_format = 'N'.$count_hubs.':N'.$total_shipper_rows;
        $sheet->getStyle($set_shipper_actual_number_format)->getNumberFormat()->setFormatCode('0.00');
        $sheet->getStyle($set_shipper_chargeable_number_format)->getNumberFormat()->setFormatCode('0.00');


        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="daily_pickup_sales_report.xlsx"');
        header('Cache-Control: max-age=0');
        $date_file_name = Carbon::parse($date)->format('Y_m_d');
        $time_string = Carbon::now()->toTimeString();
        $time_string = Carbon::parse($time_string)->format('h_i_s');
        $city_name = '';
        $file_name_without_path = '';
        if($search_city != null){
            $city_name = $city->name;
        }
        if($sales_tagging == TRUE){
            $file_name_without_path = "reports/daily_pickup_sales_report_".$date_file_name.'_'.$city_name.$time_string.".xlsx";
            $file_name = public_path() .'/'.$file_name_without_path ;
        }
        else{
            $file_name_without_path = "reports/daily_pickup_sales_report_".$date_file_name.'_'.$city_name.$time_string.".xlsx";
            $file_name = public_path() . "/reports/daily_pickup_sales_report_".$date_file_name.'_'.$time_string.".xlsx";
        }
        $writer->save($file_name);

        if($sales_tagging == TRUE){
            return ['status' => 1, 'file_name' => $file_name_without_path];
        }else{
            return url('/').'/'.$file_name_without_path;
        }
    }
//    public function daily_pickup_sales_download(Request $request){
////        $file_name = "/reports/daily_pickup_sales_report_".Auth::id().".xlsx";
//        $file = $request->file;
//        $headers = array('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',);
//        return Response::download($file, 'daily_pickup_sales_report.xlsx',$headers);
//    }

    public function customer_sales_index(Request $request){
        if (session('role_id') == 1){
            $shippers = DB::connection('reports')->table('users')->where('status','>=',3)->get();
            $hubs = DB::connection('reports')->table('cities')->select('id','name')->where('hub',1)->get();
        }else{
            $hubs = DB::connection('reports')->table('cities')->select('id','name')->whereIn('id',session('hubs'))->get();
            if(session('department_id') != 7){
                $shippers = DB::connection('reports')->table('users')->where('status',3)->whereExists(function ($query) {
                    $query->from('cities')
                    ->where('users.city_id', '=', DB::raw('`cities`.`id`'))
                    ->whereIn('hub_id', session('hubs'));
                })->get();

            }else{
                if(session('role_id') != 4){
                    $shippers = DB::connection('reports')->table('users')->where('status',3)->whereIn('id', session('tagged_shippers'))->whereExists(function ($query) {
                        $query->from('cities')
                        ->where('users.city_id', '=', DB::raw('`cities`.`id`'))
                        ->whereIn('hub_id', session('hubs'));
                    })->get();
                }else{
                    $shippers = DB::connection('reports')->table('users')->where('status',3)->whereExists(function ($query) {
                        $query->from('cities')
                        ->where('users.city_id', '=', DB::raw('`cities`.`id`'))
                        ->whereIn('hub_id', session('hubs'));
                    })->get();

                }
            }
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
                $city = DB::connection('reports')->table('cities')->where('id',$hub)->select('id','name')->get();
            }else{
                $city = DB::connection('reports')->table('cities')->where('hub',1)->select('id','name')->get();
            }
        }else{
            if($hub != null){
                $city = DB::connection('reports')->table('cities')->where('id',$hub)->select('id','name')->get();
            }else{
                $city = DB::connection('reports')->table('cities')->whereIn('id',session('hubs'))->select('id','name')->get();
            }

        }


        $details = array();
        $shippers = array();
//        unset($months_array[0]);

        $details['header'] = ['Origin', 'Client Account No.', 'Client Name' ];
        $details['subheader'] = ['Parcels', 'Weight','Collection Amount','Revenue' ];

        foreach ($months_array as $month){
            $details['months'][] = $month;
        }
        $hubs = array();
        $users = array();
        foreach ($city as $c) {
            $details['hubs'][$c->id] = $c->name;
            if ($shipper_filter != '') {
                $shippers = DB::connection('reports')->table('users')->where('id', $shipper_filter)->whereExists(function ($query) use ($c) {
                        $query->from('cities')
                        ->where('users.city_id', '=', DB::raw('`cities`.`id`'))
                        ->where('hub_id', '=', $c->id);
                    });
            } else {
                if(session('department_id') != 7){
                    $shippers = DB::connection('reports')->table('users')->whereExists(function ($query) use ($c) {
                        $query->from('cities')
                        ->where('users.city_id', '=', DB::raw('`cities`.`id`'))
                        ->where('hub_id', '=', $c->id);
                    });
                }else{
                    if(session('role_id') != 4){
                        $shippers = DB::connection('reports')->table('users')->whereExists(function ($query) use ($c) {
                            $query->from('cities')
                            ->where('users.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('hub_id', '=', $c->id);
                        })->whereIn('users.id', session('tagged_shippers'));
                    }else{
                        $shippers = DB::connection('reports')->table('users')->whereExists(function ($query) use ($c) {
                            $query->from('cities')
                            ->where('users.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('hub_id', '=', $c->id);
                        });
                    }
                }

            }

            if ($shippers->exists()) {
                foreach ($shippers->get() as $key => $s) {
                    $details['shipper'][$c->id][$s->id] = $s->name;
                    foreach ($months_array as $month) {
                        $thisMonth = Carbon::parse($month)->month;
                        $thisYear = Carbon::parse($month)->year;
                        $details['parcels'][$s->id][$month] = DB::connection('reports')->table('shipments')->where('user_id', $s->id)
                            ->whereExists(function($query) use ($thisMonth,$thisYear) {
                                $query->from('shipments_journey')
                                ->where('shipments.id', '=', DB::raw('`shipments_journey`.`shipment_id`'))
                                ->whereMonth('created_at', $thisMonth)
                                ->whereYear('created_at', $thisYear)
                                ->where('shipper_status_id', 2);
                            })->count();
                        $details['weight'][$s->id][$month] = DB::connection('reports')->table('shipments')->where('user_id', $s->id)->whereExists(function($query) use ($thisMonth,$thisYear) {
                            $query->from('shipments_journey')
                                ->where('shipments.id', '=', DB::raw('`shipments_journey`.`shipment_id`'))
                                ->whereMonth('created_at', $thisMonth)
                                ->whereYear('created_at', $thisYear)
                                ->where('shipper_status_id', 2);
                        })->sum('actual_weight');
                        $details['amount'][$s->id][$month] = number_format(DB::connection('reports')->table('shipments')->where('user_id', $s->id)->whereExists(function($query) use ($thisMonth,$thisYear) {
                            $query->from('shipments_journey')
                                ->where('shipments.id', '=', DB::raw('`shipments_journey`.`shipment_id`'))
                                ->whereMonth('created_at', $thisMonth)
                                ->whereYear('created_at', $thisYear)
                                ->where('shipper_status_id', 2);
                        })->sum('amount'));
                        $details['revenue'][$s->id][$month] = number_format(DB::connection('reports')->table('shipments')->where('user_id', $s->id)->whereExists(function($query) use ($thisMonth,$thisYear) {
                            $query->from('shipments_journey')
                                ->where('shipments.id', '=', DB::raw('`shipments_journey`.`shipment_id`'))
                                ->whereMonth('created_at', $thisMonth)
                                ->whereYear('created_at', $thisYear)
                                ->where('shipper_status_id', 2);
                        })->sum(DB::connection('reports')->raw('IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)')));

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

        $cellIndexcol1 = 4;
        $cellIndexcol2 = 7;

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
        $parcelIndex = 4;
        $weightIndex = 5;
        $codIndex = 6;
        $revenueIndex = 7;
        foreach ($details['hubs'] as $key => $h) {

            $sheet->setCellValue('A'.$col,$h);
            $sheet->getStyle('A'.$col)->applyFromArray($cell_st);

            if (isset($details['shipper']) && !empty($details['shipper'])) {
                foreach ($details['shipper'] as $hkey => $client){
                    foreach ($client as $ship_key => $cli){
                        if($hkey == $key){
                            $sheet->setCellValue('B'.$col,str_pad($ship_key, 6, '0', STR_PAD_LEFT));
                            $sheet->setCellValue('C'.$col,$cli);
                            $parcelIndex = 4;
                            $weightIndex = 5;
                            $codIndex = 6;
                            $revenueIndex = 7;
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
        $file_name = "reports/customer_sales_report".Auth::id().".xlsx";
        $writer->save("$file_name");
        return response()->json(['success'=>1,'file'=>'customer_sales_report.xlsx']);
    }
    public function customer_sales_download(Request $request){
        $file_name = "/reports/customer_sales_report".Auth::id().".xlsx";

        $file = public_path().$file_name;
        $headers = array('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',);
        return Response::download($file, 'customer_sales_report.xlsx',$headers);
    }
    public function completed_delivery_notes_index(){
        $riders = DB::connection('reports')->table('riders')->get(['id','name']);
        $admins = DB::connection('reports')->table('admins')->get(['id','name']);
        $hubs = DB::connection('reports')->table('cities')->where('hub',1)->where('status',1)->select('id','name')->get();
        return view('admin.reports.completed_delivery_notes_report')->with(['riders'=>$riders,'admins'=>$admins, 'hubs' => $hubs]);
    }
    public function completed_delivery_notes_list(Request $request){
        $deliveries = DB::connection('reports')->table('delivery_notes')->
        join('cities AS oc', 'delivery_notes.hub_id', '=', 'oc.id')
            ->leftjoin('delivery_note_shipments as dns','dns.delivery_note_id', '=', 'delivery_notes.id')
            ->leftjoin('shipments','shipments.id', '=', 'dns.shipment_id')
            ->join('riders', 'delivery_notes.rider_id', '=', 'riders.id')
            ->join('routes', 'delivery_notes.route_id', '=', 'routes.id')
            ->leftjoin('admins as ccb', 'delivery_notes.cash_collected_by', '=', 'ccb.id')
            ->join('admins','admins.id','=','delivery_notes.admin_id')
            ->leftjoin('admins as ub','ub.id','=','delivery_notes.updated_by')
            ->leftjoin('admins as vb','vb.id','=','delivery_notes.verified_by')
            ->select(['delivery_notes.id as delivery_note','delivery_notes.id as delivery_note_id','oc.id as hub_id','oc.name as hub','riders.name as rider','routes.code as route','routes.start','routes.end','admins.name as assignee','ub.name as updated_by','delivery_notes.updated_at as updated_at','delivery_notes.delivered_shipments','delivery_notes.created_at as created_at','delivery_notes.total_cod_amount as amount','delivery_notes.shipments_count','delivery_notes.last_updated_at','vb.name as verified_by','delivery_notes.status_updated_at as status_updated','delivery_notes.status_verified_at as status_verified', 'delivery_notes.cash_collected_by','ccb.name as cash_collected', 'delivery_notes.cash_collected_at'])
            ->where('delivery_notes.status',1)->groupBy('delivery_notes.id');
        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('delivery_notes.hub_id', session('hubs'));
        }
        if(session('department_id') == 7){
            if(session('role_id') != 4 ){
                $deliveries = $deliveries->whereIn('shipments.user_id', session('tagged_shippers'));
            }
        }
        $datatable = Datatables::of($deliveries)
            ->addColumn('aging_create_update',function ($deliveries){
                return ($deliveries->created_at && $deliveries->updated_at)? Carbon::parse($deliveries->updated_at)->diffInDays($deliveries->created_at) :'-';
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->addColumn('aging_update_verified',function ($deliveries){
                return ($deliveries->updated_at && $deliveries->status_verified)? Carbon::parse($deliveries->status_verified)->diffInDays($deliveries->updated_at) :'-';
            })
            ->addColumn('aging_create_verified',function ($deliveries){
                return ($deliveries->created_at && $deliveries->status_verified)? Carbon::parse($deliveries->status_verified)->diffInDays($deliveries->created_at) :'-';
            })
            ->editColumn('delivery_note', function ($deliveries) {
                return str_pad($deliveries->delivery_note, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('delivery_note_link', function ($deliveries) {
                return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($deliveries->delivery_note, 6, '0', STR_PAD_LEFT) . '</span></button>';
            })
            ->editColumn('shipments_count_link', function($deliveries) {
                if ($deliveries->shipments_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->shipments_count . '</button>';
                }
                else {
                    return 0;
                }
            })
            ->editColumn('delivered_shipments_link', function($deliveries) {
                if ($deliveries->delivered_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->delivered_shipments . '</button>';
                }
                else {
                    return 0;
                }
            })
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
        if ($hub = $request->get('search_hub')) {
            $datatable->where('oc.id', '=', $hub);
        }
        if($submission_date = $request->get('search_submission')){
            $datatable->whereDate('delivery_notes.updated_at',$submission_date);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable->whereBetween('delivery_notes.created_at', [$from,$to]);
        }
        if ($request->get('update_date_from') && $request->get('update_date_to')) {
            $from = $request->get('update_date_from');
            $to = $request->get('update_date_to');
            $datatable->whereBetween('delivery_notes.status_updated_at', [$from,$to]);
        }
        return $datatable->make(true);

    }
    //completed_delivery_note
    public function completed_shipments(Request $request){
        $delivery_note_id = $request->input('delivery_note_id');
        $delivery_note_shipments = DB::connection('reports')->table('delivery_note_shipments')->where('delivery_note_id', $delivery_note_id)->get();
        $shipments = array();
        if($delivery_note_shipments->count() != 0){
            foreach ($delivery_note_shipments as $delivery_note_shipment){
                $shipment = DB::connection('reports')->table('shipments')->find($delivery_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Delivery Note Shipments', 'shipments' => $shipments];
        }else{
            return ['status' => 0, 'success' => 'No Delivery Note Shipments', 'shipments' => FALSE];
        }
    }
    public function completed_shipments_delivered(Request $request){
        $delivery_note_id = $request->input('delivery_note_id');
        $delivery_note_shipments = DB::connection('reports')->table('delivery_note_shipments')->where('delivery_note_id', $delivery_note_id)->where('status', '>', 1)->get();
        $shipments = array();
        if($delivery_note_shipments->count() != 0){
            foreach ($delivery_note_shipments as $delivery_note_shipment){
                $shipment = DB::connection('reports')->table('shipments')->find($delivery_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Delivery Note Shipments', 'shipments' => $shipments];
        }else{
            return ['status' => 0, 'success' => 'No Delivery Note Shipments', 'shipments' => FALSE];
        }
    }

    public function customer_retention_index(){
        if (session('role_id') == 1) {
            $shippers = DB::connection('reports')->table('users')->where('status','>=',3)->get();
            $hubs = DB::connection('reports')->table('cities')->select('id','name')->where('hub',1)->get();
        }else{
            if(session('department_id') != 7){
                $shippers = DB::connection('reports')->table('users')->whereExists(function ($query) {
                    $query->from('cities')
                    ->where('users.city_id', '=', DB::raw('`cities`.`id`'))
                    ->whereIn('hub_id', session('hubs'));
                })->where('status','>=',3)->get();
                $hubs = DB::connection('reports')->table('cities')->select('id','name')->whereIn('id',session('hubs'))->get();
            }else{
                if(session('role_id') != 4){
                    $shippers = DB::connection('reports')->table('users')->whereIn('id', session('tagged_shippers'))->where('status','>=',3)->get();
                    $hubs = DB::connection('reports')->table('cities')->select('id','name')->whereIn('id',session('hubs'))->get();
                }else{
                    $shippers = DB::connection('reports')->table('users')->whereExists(function ($query) {
                        $query->from('cities')
                        ->where('users.city_id', '=', DB::raw('`cities`.`id`'))
                        ->whereIn('hub_id', session('hubs'));
                    })->where('status','>=',3)->get();
                    $hubs = DB::connection('reports')->table('cities')->select('id','name')->whereIn('id',session('hubs'))->get();
                }
            }
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
//        unset($months_array[0]);


        $details = array();
        $shippers = array();
        $shippers['header'] = ['S.No','Client Account No.','Client Name'];



        foreach($months_array as $month){

            $first_date  = Carbon::parse($month)->firstOfMonth();
            $last_date  = Carbon::parse($month)->lastOfMonth()->endOfDay();
            if($hub != null){
                if(session('department_id') != 7){
                    $details['s'][$month] = DB::connection('reports')->table('users')->whereDate('activated_at','<=',$first_date)->where('status',3)->where('city_id',$hub)->count();
                    $details['e'][$month] = DB::connection('reports')->table('users')->whereDate('activated_at','<=',$last_date)->where('status',3)->where('city_id',$hub)->count();
                    $details['n'][$month] = DB::connection('reports')->table('users')->whereBetween('activated_at',[$first_date,$last_date])->where('status',3)->where('city_id',$hub)->count();
                }else{
                    if(session('role_id') != 4){
                        $details['s'][$month] = DB::connection('reports')->table('users')->whereDate('activated_at','<=',$first_date)->where('status',3)->where('city_id',$hub)->whereIn('id', session('tagged_shippers'))->count();
                        $details['e'][$month] = DB::connection('reports')->table('users')->whereDate('activated_at','<=',$last_date)->where('status',3)->where('city_id',$hub)->whereIn('id', session('tagged_shippers'))->count();
                        $details['n'][$month] = DB::connection('reports')->table('users')->whereBetween('activated_at',[$first_date,$last_date])->where('status',3)->where('city_id',$hub)->whereIn('id', session('tagged_shippers'))->count();
                    }else{
                        $details['s'][$month] = DB::connection('reports')->table('users')->whereDate('activated_at','<=',$first_date)->where('status',3)->where('city_id',$hub)->count();
                        $details['e'][$month] = DB::connection('reports')->table('users')->whereDate('activated_at','<=',$last_date)->where('status',3)->where('city_id',$hub)->count();
                        $details['n'][$month] = DB::connection('reports')->table('users')->whereBetween('activated_at',[$first_date,$last_date])->where('status',3)->where('city_id',$hub)->count();
                    }
                }
            }else{
                if(session('department_id') != 7){
                    $details['s'][$month] = number_format(DB::connection('reports')->table('users')->whereDate('activated_at','<=',$first_date)->where('status',3)->count());
                    $details['e'][$month] = number_format(DB::connection('reports')->table('users')->whereDate('activated_at','<=',$last_date)->where('status',3)->count());
                    $details['n'][$month] = number_format(DB::connection('reports')->table('users')->whereBetween('activated_at',[$first_date,$last_date])->where('status',3)->count());
                }else{
                    if(session('role_id') != 4){
                        $details['s'][$month] = number_format(DB::connection('reports')->table('users')->whereDate('activated_at','<=',$first_date)->where('status',3)->whereIn('id', session('tagged_shippers'))->count());
                        $details['e'][$month] = number_format(DB::connection('reports')->table('users')->whereDate('activated_at','<=',$last_date)->where('status',3)->whereIn('id', session('tagged_shippers'))->count());
                        $details['n'][$month] = number_format(DB::connection('reports')->table('users')->whereBetween('activated_at',[$first_date,$last_date])->where('status',3)->whereIn('id', session('tagged_shippers'))->count());
                    }else{
                        $details['s'][$month] = number_format(DB::connection('reports')->table('users')->whereDate('activated_at','<=',$first_date)->where('status',3)->count());
                        $details['e'][$month] = number_format(DB::connection('reports')->table('users')->whereDate('activated_at','<=',$last_date)->where('status',3)->count());
                        $details['n'][$month] = number_format(DB::connection('reports')->table('users')->whereBetween('activated_at',[$first_date,$last_date])->where('status',3)->count());
                    }
                }
            }

            $n = ($details['s'][$month] != 0)? $details['s'][$month]:0;
            $details['crr'][$month] = ($n != 0)? (($details['e'][$month]-$details['n'][$month])/$n)*100 :'-';

            $shippers['header'][] = $month;

        }

        if($hub != null){
            if (session('role_id') == 1 || in_array($hub, session('hubs'))) {
                if(session('department_id') == 7 ){
                    if(session('role_id') != 4){
                        $shippers['shipper'] = DB::connection('reports')->table('users')->where('status','>=',3)->whereIn('id', session('tagged_shippers'))->get();
                    }else{
                        $shippers['shipper'] = DB::connection('reports')->table('users')->whereExists(function ($query) use ($hub) {
                            $query->from('cities')
                            ->where('users.city_id', '=', 'cities.id')
                            ->where('hub_id', '=', $hub);
                        })->where('status','>=',3)->get();
                    }
                }else{

                    $shippers['shipper'] = DB::connection('reports')->table('users')->whereExists(function ($query) use ($hub) {
                        $query->from('cities')
                        ->where('users.city_id', '=', 'cities.id')
                        ->where('hub_id', '=', $hub);
                    })->where('status','>=',3)->get();
                }
            }
            else {
                $shippers['shipper'] = '';
            }
        }
        else{
            if (session('role_id') == 1) {
                $shippers['shipper'] = DB::connection('reports')->table('users')->where('status','>=',3)->get();
            }
            else {
                if(session('department_id') == 7){
                    if(session('role_id') != 4){
                        $shippers['shipper'] = DB::connection('reports')->table('users')->where('status','>=',3)->whereIn('id', session('tagged_shippers'))->get();
                    }else{
                        $shippers['shipper'] = DB::connection('reports')->table('users')->whereExists(function ($query) {
                            $query->from('cities')
                            ->where('users.city_id', '=', 'cities.id')
                            ->where('hub_id', '=', session('hubs'));
                        })->where('status','>=',3)->get();
                    }
                }else{
                    $shippers['shipper'] = DB::connection('reports')->table('users')->whereExists(function ($query) {
                        $query->from('cities')
                        ->where('users.city_id', '=', 'cities.id')
                        ->where('hub_id', '=', session('hubs'));
                    })->where('status','>=',3)->get();
                }
            }
        }


        if($shipper_filter != null){
            if (session('role_id') == 1){
                $client_exist =DB::connection('reports')->table('users')->where('id',$shipper_filter);
                if($client_exist->exists()){
                    $shippers['shipper'] = $client_exist->get();
                }
            }else{
                $client_exist =DB::connection('reports')->table('users')->where('id',$shipper_filter)
                    ->whereExists(function ($query) {
                        $query->from('cities')
                        ->where('users.city_id', '=', 'cities.id')
                        ->where('hub_id', '=', session('hubs'));
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
                    $shippers['parcels'][$client->id][$month] = DB::connection('reports')->table('shipments')->where('user_id', $client->id)
                        ->whereExists(function($query) use ($thisMonth,$thisYear) {
                            $query->from('shipments_journey')
                            ->where('shipments.id', '=', DB::raw('`shipments_journey`.`shipment_id`'))
                            ->whereMonth('created_at', $thisMonth)
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
        $dateIndex = 4;
        if(count($shippers['shipper']) > 0) {
            foreach ($shippers['name'] as $id => $shipper) {
                $sheet->setCellValue('A' . $col, $serials);
                $sheet->setCellValue('B' . $col, str_pad($id, 6, '0', STR_PAD_LEFT));
                $sheet->setCellValue('C' . $col, $shipper);
                foreach ($months_array as $m) {
                    $cellIndexShipper = Coordinate::stringFromColumnIndex($dateIndex);
                    $sheet->setCellValue($cellIndexShipper . $col, $shippers['parcels'][$id][$m]);
                    $dateIndex++;
                }
//            $sheet->setCellValue($dateIndex.$col,$shippers['total'][$id]);
                $col++;
                $serials++;
                $dateIndex = 4;
            }
        }
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="customer_retention_report.xlsx"');
        header('Cache-Control: max-age=0');
        $file_name = "reports/customer_retention_report".Auth::id().".xlsx";
        $writer->save("$file_name");
        return response()->json(['success'=>1,'file'=>'customer_retention_report.xlsx']);

    }
    public function customer_retention_download(Request $request){
        $file_name = "/reports/customer_retention_report".Auth::id().".xlsx";
        $file = public_path().$file_name;
        $headers = array('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',);
        return Response::download($file, 'customer_retention_report.xlsx',$headers);
    }
    public function overall_sales_index(){
        $shippers = DB::connection('reports')->table('users')->whereIn('status',[3,4])->select('id','name')->get();
        $cities = DB::connection('reports')->table('cities')->select('id','name')->get();
        $hubs = DB::connection('reports')->table('cities')->where('hub',1)->select('id','name')->get();
        $statuses = DB::connection('reports')->table('shipment_status')->whereNotIn('id',[1,17])->get();
        return view('admin.reports.overall_sales')->with(['shippers'=>$shippers,'cities'=>$cities,'hubs'=>$hubs,'statuses'=>$statuses]);
    }
    public function overall_sales_list(Request $request){

        $sales = DB::connection('reports')->table('shipments')->join('users as u','u.id','=','shipments.user_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->leftjoin('zones as z', 'z.id', '=', 'oc.zone_id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->leftjoin('zone_class_cities as zcc', function($join){
                $join->on('z.id', '=', 'zcc.zone_id')
                ->on('dc.id', '=', 'zcc.city_id')
                ->on('zone_classification_id', '=', DB::connection('reports')->raw('IF (shipments.shipping_mode_id IN (1, 4), 1, 2)'));
            })
            ->join('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=' , 'sps.id')
            ->leftjoin('delivery_note_shipments as ds',function($join){
                $join->on('ds.shipment_id','=','shipments.id')
                    ->where('ds.delivery_note_id','=',
                        DB::connection('reports')->raw('(select max(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = shipments.id and delivery_note_shipments.status > 3 and  delivery_note_shipments.status != 8)')                        );
            })
            ->leftjoin('delivery_note_station_deposit_notes as dnsdn', 'ds.delivery_note_id', '=', 'dnsdn.delivery_note_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id','=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('pending_payment_shipments as pps', function ($join) {
                $join->on('pps.shipment_id', '=', 'shipments.id')
                    ->where('pps.id','=',
                        DB::connection('reports')->raw('(select max(id) from pending_payment_shipments where pending_payment_shipments.shipment_id = shipments.id)'));
            })
            ->leftJoin('done_payment_shipments as dps', function ($join) {
                $join->on('dps.shipment_id', '=', 'shipments.id')
                    ->where('dps.id','=',
                        DB::connection('reports')->raw('(select max(id) from done_payment_shipments where done_payment_shipments.shipment_id = shipments.id)'));
            })
            ->leftJoin('shipments_journey as dr', function ($join) {
                $join->on('dr.shipment_id', '=', 'shipments.id')
                    ->where('dr.id','=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,20,30,36,37))'));
            })
            ->leftjoin('shipment_items as si', function ($join) {
                $join->on('si.shipment_id', '=', 'shipments.id')
                    ->where('si.type','=',0);
            })
            ->leftjoin('sale_person_tags as spt', function ($join) {
                $join->on('spt.user_id', '=', 'shipments.user_id')
                    ->leftjoin('admins as adsp','adsp.id','=','spt.admin_id')
                    ->where('spt.status','=',0);
            })
            ->leftjoin('products as p','p.id','=','si.product_type_id')
            ->leftJoin('pending_invoice_shipments as pis', function ($join) {
                $join->on('pis.shipment_id', '=', 'shipments.id')
                    ->where('pis.id','=',
                        DB::connection('reports')->raw('(select max(id) from pending_invoice_shipments where pending_invoice_shipments.shipment_id = shipments.id)'));
            })
            ->leftJoin('invoice_shipments as is', function ($join) {
                $join->on('is.shipment_id', '=', 'shipments.id')
                    ->where('is.id','=',
                        DB::connection('reports')->raw('(select max(id) from invoice_shipments where invoice_shipments.shipment_id = shipments.id)'));
            })
			->select('p.product_name as category','si.description as description','shipments.id as shipment_id','shipments.tracking_number','shipments.order_id as order_id','shipments.tracking_number as tracking_number_link','u.id as account_no','u.name as shipper','ss.name as current_status','bt.booking_type as service_type','sj.created_at as arrival_date','oc.name as origin','dc.name as destination','h.name as hub','shipments.amount as s_collection_amount','sps.name as payment_status','pps.amount as p_collection_amount','shipments.actual_weight','shipments.weight_charges','shipments.cash_handling_charges','shipments.insurance_charges','shipments.return_charges','shipments.replacement_charges','shipments.fuel_surcharge','shipments.try_and_buy_charges','shipments.packaging_material_charges','pps.gst as p_gst','pps.charges as p_total_charges','pps.payable as p_net_payable','dps.amount as d_collection_amount','dps.gst as d_gst','dps.charges as d_total_charges','dps.payable as d_net_payable', 'sm.mode as shipping_mode','shipments.chargeable_weight','dr.created_at as delivered_or_returned','z.name as zone','zcc.class', 'oc.id as origin_city_id', 'dc.id as destination_city_id', 'dnsdn.station_deposit_note_id as sdn_id', 'dps.done_payment_id as payment_id', 'shipments.booking_type_id', 'usi.poc', 'adsp.name as sales_person', 'shipments.shipper_status_id as shipment_status', 'shipments.nsa_osa_charges', 'u.account_type_id as account_type_id', 'pis.gst as pis_gst', 'is.gst as is_gst')
            ->whereNotIn('shipments.shipper_status_id',[1,17]);
//        if (!$request->get('search_date_from') && !$request->get('search_date_to')) {
//            $now = Carbon::now();
//            $yesterday = Carbon::now()->subDays(3);
//            $sales = $sales->whereBetween('sj.created_at', [$yesterday,$now]);
//        }

        if (session('role_id') != 1) {
            if (session('department_id') == 7 && session('role_id') != 4) {
                $sales = $sales->whereIn('u.id', session('tagged_shippers'));
            }
            else {
                $sales = $sales->whereIn('dc.hub_id', session('hubs'));
            }
        }

        $datatable = Datatables::of($sales)
            ->addColumn('attempts', function($shipment){
                $out_for_delivery = DB::connection('reports')->table('shipments_journey')->where('shipment_id',$shipment->shipment_id)->where('shipper_status_id',5)->count();
                return $out_for_delivery;
            })
            ->editColumn('account_no', function ($shipments) {
                return str_pad($shipments->account_no, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('insurance_charges', function($shipment){
                return number_format($shipment->insurance_charges, 2);
            })
            ->editColumn('cash_handling_charges', function($shipment){
                if($shipment->shipment_status == 20 || $shipment->shipment_status == 21 || $shipment->shipment_status == 22 || $shipment->shipment_status == 23 || $shipment->shipment_status == 23 || $shipment->shipment_status == 25){
                    return "-";
                }
                else{
                    if($shipment->cash_handling_charges != null){
                        return number_format($shipment->cash_handling_charges, 2);
                    }
                    else{
                        return "-";
                    }
                }
            })
            ->editColumn('return_charges', function($shipment){
                return number_format($shipment->return_charges, 2);
            })
            ->editColumn('replacement_charges', function($shipment){
                return number_format($shipment->replacement_charges, 2);
            })
            ->editColumn('try_and_buy_charges', function($shipment){
                return number_format($shipment->try_and_buy_charges, 2);
            })
            ->editColumn('nsa_osa_charges', function($shipment){
                return number_format($shipment->nsa_osa_charges, 2);
            })
            ->editColumn('packaging_material_charges', function($shipment){
                return number_format($shipment->packaging_material_charges, 2);
            })
            ->editColumn('p_total_charges', function($shipment){
                return number_format($shipment->p_total_charges, 2);
            })
            ->editColumn('d_total_charges', function($shipment){
                return number_format($shipment->d_total_charges, 2);
            })
            ->editColumn('p_net_payable', function($shipment){
                return number_format($shipment->p_net_payable, 2);
            })
            ->editColumn('d_net_payable', function($shipment){
                return number_format($shipment->d_net_payable, 2);
            })
            ->editColumn('d_gst', function($shipment){
                return number_format($shipment->d_gst, 2);
            })
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('s_collection_amount', function($shipment){
                return number_format($shipment->s_collection_amount);
            })
            ->editColumn('d_collection_amount', function($shipment){
                return number_format($shipment->d_collection_amount);
            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper .' (' . $shipment->poc . ')';
                }
                else {
                    return $shipment->shipper;
                }
            })
            ->editColumn('p_collection_amount',function($sale){
                $amount = '';
                if($sale->p_collection_amount != null){
                    $amount = $sale->p_collection_amount;
                }else if($sale->d_collection_amount != null){
                    $amount = $sale->d_collection_amount;
                }else{
                    $amount = $sale->s_collection_amount;
                }
                return number_format($amount);
            })
            ->editColumn('p_gst',function($sale){
                $gst = '';
                if($sale->account_type_id == 1){
                    if($sale->p_gst != null){
                        $gst = $sale->p_gst;
                    }else if($sale->d_gst != null){
                        $gst = $sale->d_gst;
                    }
                }
                else{
                    if($sale->pis_gst != null){
                        $gst = $sale->pis_gst;
                    }else if($sale->is_gst != null){
                        $gst = $sale->is_gst;
                    }
                }
                return number_format((float)$gst, 2);
            })
            ->editColumn('p_total_charges',function($sale){
                $total = '';
                if($sale->p_total_charges != null){
                    $total = $sale->p_total_charges;
                }else if($sale->d_total_charges != null){
                    $total = $sale->d_total_charges;
                }
                return number_format((float)$total, 2);
            })
            ->addColumn('estimated_charges',function($sale){
                $estimated = '';
                $estimated = (($sale->weight_charges != null)? $sale->weight_charges:0) + (($sale->cash_handling_charges != null)? $sale->cash_handling_charges:0) + (($sale->insurance_charges != null)? $sale->insurance_charges:0) + (($sale->insurance_charges != null)? $sale->insurance_charges:0) + (($sale->return_charges != null)? $sale->return_charges:0) + (($sale->replacement_charges != null)? $sale->replacement_charges:0) + (($sale->fuel_surcharge != null)? $sale->fuel_surcharge:0) + (($sale->try_and_buy_charges != null)? $sale->try_and_buy_charges:0) + (($sale->packaging_material_charges != null)? $sale->packaging_material_charges:0);
                return number_format((float)$estimated, 2);
            })
            ->editColumn('p_net_payable',function($sale){
                $payable = '';
                if($sale->p_net_payable != null){
                    $payable = $sale->p_net_payable;
                }else if($sale->d_net_payable != null){
                    $payable = $sale->d_net_payable;
                }
                return number_format((float)$payable, 2);
            })
            ->addColumn('class',function($sale){
                $class = '';

                if ($sale->origin_city_id != $sale->destination_city_id) {
                    switch ($sale->class) {
                        case 0:
                            $class = 'Class A';
                            break;
                        case 1:
                            $class = 'Class B';
                            break;
                        case 2:
                            $class = 'Class C';
                            break;
                        case 3:
                            $class = 'Class D';
                            break;
                    }
                }

                return $class;
            });

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
            $datatable->whereBetween('sj.created_at', [$from,$to]);
        }
        return $datatable->make(true);
    }

    public function sales_person_performance_index(){
        if (session('role_id') == 1) {
            $sales_persons = DB::connection('reports')->table('admins')->join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name'])->where('ar.department_id', 7)->get();
            $hubs = DB::connection('reports')->table('cities')->select('id','name')->where('hub',1)->get();
        }else{
            if(session('department_id') != 7){
                $sales_persons = DB::connection('reports')->table('admins')->join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name'])->where('ar.department_id', 7)->get();
                $hubs = DB::connection('reports')->table('cities')->select('id','name')->whereIn('id',session('hubs'))->get();
            }else{
                if(session('role_id') != 4){
                    $sales_persons = DB::connection('reports')->table('admins')->where('id', Auth::id())->select('id', 'name')->get();
                    $hubs = DB::connection('reports')->table('cities')->select('id','name')->whereIn('id',session('hubs'))->get();
                }else{
                    $sales_persons = DB::connection('reports')->table('admins')->join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name'])->where('ar.department_id', 7)->get();
                    $hubs = DB::connection('reports')->table('cities')->select('id','name')->whereIn('id',session('hubs'))->get();
                }
            }
        }
        return view('admin.reports.sales_person_performance_report')->with(['hubs'=>$hubs,'sales_persons'=>$sales_persons]);
    }
    public function sales_person_performance_export_to_excel(Request $request){
        $hub = $request->city;
        $sales_person_filter = $request->sales_person;
        $start_date = $request->from_date;
        $current_date = $request->to_date;

        $start_date = Carbon::parse($start_date);
        $current_date = Carbon::parse($current_date);
        $account_status = array();
        if($request->account == ''){
            $account_status = [3,4,5];
        }else if($request->account == 3){
            $account_status = [3];
        }else if($request->account == 4){
            $account_status = [4];
        }else if($request->account == 5){
            $account_status = [5];
        }

        $dates = [];

        for($d = $start_date; $d->lte($current_date); $d->addDay()) {
            $dates[] = $d->format('Y-m-d');
        }

        if($sales_person_filter != null){
            $sales_person = DB::connection('reports')->table('admins')->where('id', $sales_person_filter)->get();

        }else{
            if(session('role_id') == 1){
                $sales_person = DB::connection('reports')->table('admins')->join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name'])->where('ar.department_id', 7)->get();
            }else{
                if(session('department_id') != 7){
                    $sales_person = DB::connection('reports')->table('admins')->join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name'])->where('ar.department_id', 7)->get();
                }else{
                    if(session('role_id') != 4){
                        $sales_person = DB::connection('reports')->table('admins')->where('id', Auth::id())->get();
                    }else{
                        $sales_person = DB::connection('reports')->table('admins')->join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name'])->where('ar.department_id', 7)->get();
                    }
                }
            }
        }

        $details = array();
        $date_sums = array();

        foreach ($dates as $date){
            $details['dates'][] = $date;

            $date_sums[$date] = 0;
        }
        $overall_sum = 0;
        $sales_persons_data = array();
        $shippers = array();

        $details['header'] = ['Sales Persons', 'Client Account No', 'Client Name' ];
//        $details['subheader'] = ['Parcels', 'Weight','Collection Amount','Revenue' ];

        foreach ($sales_person as $person){
            $sales_persons_data[$person->id]['name'] = $person->name;

            $tagged_shippers = DB::connection('reports')->table('sale_person_tags')->where('admin_id', $person->id)->where('status', 0)->select('user_id')->get();
            foreach ($tagged_shippers as $shipper){
                if($hub != null){

                    $user = DB::connection('reports')->table('users')->whereExists(function ($query) use ($hub) {
                        $query->from('cities')
                        ->where('users.city_id', '=', DB::raw('`cities`.`id`'))
                        ->where('hub_id', '=', $hub);
                    })->where('id', $shipper->user_id)->whereIn('status',$account_status)->first();
                }else{

                    $user = DB::connection('reports')->table('users')->where('id',$shipper->user_id)->whereIn('status',$account_status)->first();
                }
                if($user){

                    $sales_persons_data[$person->id]['shipper'][$user->id] = $user->name;
//                    $sales_persons_data[$person->id]['account'][$user->id] = str_pad($user->id, 6, '0', STR_PAD_LEFT);
                    foreach ($dates as $date){
                        if($hub != null){
                            $sum = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($date) {
                                $query->from('shipments_journey')
                                ->where('shipments.id', '=', DB::raw('`shipments_journey`.`shipment_id`'))
                                ->whereDate('created_at', $date)
                                ->where('shipper_status_id', 2);
                            })->whereExists(function($query) use ($hub) {
                                $query->from('user_shipping_infos')
                                ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                                ->whereExists(function($sub_query) use ($hub) {
                                    $sub_query->from('cities')
                                    ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                                    ->where('cities.id', $hub);
                                });
                            })->where('shipments.user_id', $user->id)->count();
                        }else{
                            $sum = DB::connection('reports')->table('shipments')->whereExists(function ($query) use ($date) {
                                $query->from('shipments_journey')
                                ->where('shipments.id', '=', DB::raw('`shipments_journey`.`shipment_id`'))
                                ->whereDate('created_at', $date)
                                ->where('shipper_status_id', 2);
                            })->where('shipments.user_id', $user->id)->count();
                        }


                        $sales_persons_data[$person->id]['pickups'][$user->id][] = $sum;

                        $date_sums[$date] += $sum;
                        $overall_sum += $sum;
                    }

                    $sum_of_pickups = array_sum($sales_persons_data[$person->id]['pickups'][$user->id]);
                    array_push($sales_persons_data[$person->id]['pickups'][$user->id],number_format($sum_of_pickups));
                }

            }
        }
//        return $sales_persons_data;

        array_push($date_sums, $overall_sum);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultColumnDimension()->setWidth(20);
        $cell_st =[
            'font' =>['bold' => true],
            'alignment' =>['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders'=>['bottom' =>['style'=> \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
        ];
        $footer_cell_st =[
            'font' =>['bold' => true]
        ];

        $admin_index = 2;
        $shipper_index = 2;
        $pickup_index = 2;
        $pickup_col_index = 4;

        foreach ($sales_persons_data as $sales_persons) {


            if(!empty($sales_persons['shipper'])) {
                $sheet->setCellValue('A'.$admin_index, $sales_persons['name']);
                foreach ($sales_persons['shipper'] as $key => $person) {

                    $sheet->setCellValue('B' . $shipper_index, str_pad($key, 6, '0', STR_PAD_LEFT));
                    $sheet->setCellValue('C' . $shipper_index, $person);

                    $shipper_index++;
                    $admin_index++;

                    foreach ($sales_persons['pickups'][$key] as $id => $pickup){

//                        foreach ($pickup as $n){
                        $cellIndex = Coordinate::stringFromColumnIndex($pickup_col_index);
                        $sheet->setCellValue($cellIndex.$pickup_index, $pickup);
                        $pickup_col_index++;
//                            $pickup_index++;
//                        }

                    }

                    $pickup_col_index = 4;
                    $pickup_index++;
                }

            }

        }
        $pickup_index += 2;
        $date_sum_col_index = 4;
        $date_sum_index = $pickup_index;
        $sheet->setCellValue('A'.$date_sum_index, "Grand Total");
        foreach ($date_sums as $date => $sum) {
            $cellIndex = Coordinate::stringFromColumnIndex($date_sum_col_index);
            $sheet->setCellValue($cellIndex . $date_sum_index, $sum);

            $date_sum_col_index++;
        }

        $cellIndexcol1 = 4;
        foreach ($details['dates'] as $key => $name) {
            $cellIndex1 = Coordinate::stringFromColumnIndex($cellIndexcol1);
            $cellIndex11 = $cellIndex1 . '1';
            $sheet->setCellValue($cellIndex11, $name);
            $cellIndexcol1 += 1;

        }
        $grand_total_index = Coordinate::stringFromColumnIndex($cellIndexcol1);
        $grand_total_header_index = $grand_total_index . '1';
        $sheet->setCellValue($grand_total_header_index,'Grand Total');
        $header_column_range = "A1:". $grand_total_header_index;
        $sheet->getStyle($header_column_range)->applyFromArray($cell_st);        //header style
        $sales_column_range = "A1:A" . $pickup_index;
        $shipper_column_range = "B1:B" . $pickup_index;
        $grand_total_last_column_range = "A".$pickup_index.":".$grand_total_index . $pickup_index;
        $sheet->getStyle($grand_total_last_column_range)->applyFromArray($footer_cell_st);        //header style
        $sheet->getStyle($sales_column_range)->applyFromArray($cell_st);        //header style
        $sheet->getStyle($shipper_column_range)->applyFromArray($cell_st);        //header style
        $sheet->fromArray($details['header'],NULL,'A1');
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="sales_person_performance.xlsx"');
        header('Cache-Control: max-age=0');
        $file_name = "reports/sales_person_performance".Auth::id()."xlsx";
        $writer->save("$file_name");
        return response()->json(['success'=>1,'file'=>'sales_person_performance.xlsx']);

    }
    public function sales_person_performance_download(Request $request){
        $file_name = "/reports/sales_person_performance".Auth::id()."xlsx";

        $file = public_path().$file_name;
        $headers = array('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',);
        return Response::download($file, 'sales_person_performance.xlsx',$headers);
    }

    public function negative_balance_customers_index()
    {
        return view('admin.reports.invoice_for_negative_balance_customers');

    }
    public function negative_balance_customers_list(Request $request)
    {
        $negative = DB::connection('reports')->table('pending_payment_shipments')->leftjoin('shipments as s','s.id','=','pending_payment_shipments.shipment_id')
            ->leftjoin('users as u','u.id','=','s.user_id')
            ->select('u.id as account_no','u.name as name','u.phone as phone','pending_payment_shipments.amount as amount','pending_payment_shipments.charges as charges','pending_payment_shipments.payable as payable')
            ->where('payable','<',0);
        $datatable = Datatables::of($negative)
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->editColumn('charges', function($shipment){
                return number_format($shipment->charges, 2);
            })
            ->editColumn('payable', function($shipment){
                return number_format($shipment->payable, 2);
            })
            ->addColumn('account_no', function ($user) {
                return str_pad($user->account_no, 6, '0', STR_PAD_LEFT);
            });
        return $datatable->make(true);

    }
    public function call_verification_index()
    {
        return view('admin.reports.call_verification_report');

    }
    public function call_verification_list(request $request)
    {
        $call_verification_report = DB::connection('reports')->table('delivery_notes')->leftjoin('delivery_note_shipments as dns', 'dns.delivery_note_id', '=', 'delivery_notes.id')
            ->leftjoin('shipments as s', 's.id', '=', 'dns.shipment_id')
            ->leftjoin('admins as ad','ad.id','=','delivery_notes.verified_by')
            ->join('shipments_journey as sj', function($join){
                $join->on('sj.shipment_id', '=', 'dns.shipment_id')
                    ->where('sj.id','=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = dns.shipment_id and shipments_journey.reference_1_id = dns.delivery_note_id and shipments_journey.verification = 1)'));
            })
            ->leftjoin('shipment_status as ss','ss.id','=','sj.shipper_status_id')
            ->select('sj.shipper_status_id as shipper_status_id','s.id as ship_id','s.tracking_number as tracking_no','s.tracking_number as tracking_number','delivery_notes.id as delivery_note_id','ss.name as status','ad.name as status_verified_by','delivery_notes.status_verified_at as status_verified_at','dns.call_verification as call_verification_status')->where('delivery_notes.verified_by','!=',null);
        $datatable = Datatables::of($call_verification_report)
            ->editcolumn('call_verification_status', function ($data){
                if($data->call_verification_status==0){
                    return 'Not Ticked';
                }
                else{
                    return 'Ticked';
                }
            })
            ->editColumn('delivery_note_id', function ($deliveries) {
                return str_pad($deliveries->delivery_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('tracking_no', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_no' class='tracking' target='_blank'>$shipments->tracking_no</a></u>";
            });
        return $datatable->make(true);

    }

    public function petty_cash_statements_index(){
        $hubs = DB::connection('reports')->table('cities')->where('hub',1)->select('id','name')->get();
        $heads = DB::connection('reports')->table('petty_cash_account_heads')->select('id', 'name')->get();
        $titles = DB::connection('reports')->table('petty_cash_account_titles')->select('id', 'name')->get();
        return view('admin.reports.petty_cash_statement')->with(['hubs' => $hubs, 'heads' => $heads, 'titles' => $titles]);
    }

    public function petty_cash_statements_list(Request $request){
        $petty = DB::connection('reports')->table('petty_cash_statement_details')->join('petty_cash_statements as pcs','pcs.id','=','petty_cash_statement_details.petty_cash_statement_id')
            ->join('cities as dc','dc.id','=', 'petty_cash_statement_details.hub_id')
            ->join('cities as h','h.id','=', 'pcs.hub_id')
            ->join('admins as cb','cb.id','=', 'pcs.created_by')
            ->leftjoin('admins as sub', 'sub.id', '=', 'petty_cash_statement_details.updated_by')
            ->leftjoin('petty_cash_account_heads as pch', 'pch.id','=','petty_cash_statement_details.account_head_id')
            ->leftjoin('petty_cash_account_titles as pct', 'pct.id','=','petty_cash_statement_details.account_title_id')
            ->select('pcs.id as statement_id','pcs.id as statement_link','dc.name as entry_city','petty_cash_statement_details.date as entry_date','pch.name as account_head','pct.name as account_title','petty_cash_statement_details.expense_details','petty_cash_statement_details.amount','petty_cash_statement_details.reference_no as entry_reference_no','petty_cash_statement_details.remarks','petty_cash_statement_details.status','pcs.reference_no as statement_reference_no','h.name as hub_name','cb.name as created_by','pcs.created_at','petty_cash_statement_details.station_amount','petty_cash_statement_details.operation_amount','petty_cash_statement_details.finance_amount');
//            ->where('petty_cash_statements.status','<',3);

        if (session('role_id') != 1) {
            $petty = $petty->whereIn('pcs.hub_id', session('hubs'));
        }

        $petty = Datatables::of($petty)
            ->editColumn('statement_link', function ($petty){
                return '<button class="btn btn-sm btn-outline-info align-middle"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . $petty->statement_link . '</span></button>';
            })
            ->addColumn('entry_date',function($petty){
                return Carbon::parse($petty->entry_date)->toDateString();
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->editColumn('status',function ($petty){
                $status = '';
                if($petty->status == 0){
                    $status = 'Created';
                }else if($petty->status == 1){
                    $status = 'Rejected';
                }else if($petty->status == 2){
                    $status = 'Approved';
                }
                return $status;
            });
        if ($hub = $request->get('search_hub')) {
            $petty->where('h.id', '=', $hub);
        }
        if ($status = $request->get('search_status')) {
            if($status == 3){
                $petty->where('petty_cash_statement_details.status', '=',0);
            }else{
                $petty->where('petty_cash_statement_details.status', $status);
            }

        }
        if ($search_date = $request->get('search_date_created')) {
            $petty->whereDate('pcs.created_at', $search_date);
        }
        if($head = $request->get('search_head')){
            $petty->where('pch.id','=',$head);
        }
        if($title = $request->get('search_title')){
            $petty->where('pct.id','=',$title);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $petty->whereBetween('petty_cash_statement_details.created_at', [$from,$to]);
        }
        return $petty->make(true);
    }

    public function fake_status_index(){
        $riders = DB::connection('reports')->table('riders')->get(['id','name']);
        $hubs = DB::connection('reports')->table('cities')->where('hub',1)->select('id','name')->get();
        return view('admin.reports.fake_statuses_report')->with(['riders' => $riders, 'hubs' => $hubs]);
    }

    public function fake_status_list(request $request){
        $delivery_note = DB::connection('reports')->table('delivery_notes')->join('delivery_note_shipments as dns','dns.delivery_note_id', '=', 'delivery_notes.id')
            ->leftjoin('shipments as s', 's.id', '=', 'dns.shipment_id')
            ->leftjoin('riders as r', 'r.id', '=', 'delivery_notes.rider_id')
            ->leftjoin('cities as c', 'c.id', '=', 'r.city_id')
            ->select('r.name as rider_name', 'c.name as rider_city', 'delivery_notes.id as delivery_note_id', 'delivery_notes.created_at', 'delivery_notes.status_verified_at as verified_at', 'delivery_notes.shipments_count as total_shipments', 'delivery_notes.delivered_shipments as delivered_shipments', DB::connection('reports')->raw('(select count(shipment_id) from delivery_note_shipments where delivery_note_shipments.delivery_note_id = delivery_notes.id and delivery_note_shipments.fake_status = 1) as shipment_fake_status'))
        ->where('dns.fake_status', 1)->groupBy('delivery_notes.id');


        $datatables = Datatables::of($delivery_note)
            ->editColumn('delivery_note_id', function ($deliveries) {
                return str_pad($deliveries->delivery_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('shipments_count_link', function($deliveries) {
                if ($deliveries->total_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->total_shipments . '</button>';
                }
                else {
                    return 0;
                }
            })
            ->editColumn('shipment_fake_status_link', function($deliveries) {
                if ($deliveries->shipment_fake_status != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->shipment_fake_status . '</button>';
                }
                else {
                    return 0;
                }
            })
            ->editColumn('undelivered_shipments_link', function($deliveries) {
                $undelivered_shipments = $deliveries->total_shipments - $deliveries->delivered_shipments;
                if ($undelivered_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $undelivered_shipments . '</button>';
                }
                else {
                    return 0;
                }
            });
        if ($rider = $request->get('rider')) {
            $datatables->where('r.id', '=', $rider);
        }
        if ($hub = $request->get('hub')) {
            $datatables->where('delivery_notes.hub_id', $hub);
        }
        if ($tracking_number = $request->get('search_tracking_no')) {
            $datatables->where('s.tracking_number', $tracking_number);
        }
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatables->where(function($query) use ($from, $to) {
                $query->where(function ($sub_query) use ($from, $to) {
                    $sub_query->WhereNull('delivery_notes.status_verified_at')
                        ->whereBetween('delivery_notes.created_at', [$from, $to]);
                })
                ->orwhere(function ($sub_query) use ($from, $to) {
                    $sub_query->WhereNotNull('delivery_notes.status_verified_at')
                        ->whereBetween('delivery_notes.status_verified_at', [$from, $to]);
                });
            });
//            $datatables->whereBetween('delivery_notes.created_at', [$from,$to]);
        }
        return $datatables->make(true);
    }

    public function fake_status_shipments_total(Request $request){
        $delivery_note_id = $request->input('delivery_note_id');
        $delivery_note_shipments = DB::connection('reports')->table('delivery_note_shipments')->where('delivery_note_id', $delivery_note_id)->get();
        $shipments = array();
        if($delivery_note_shipments->count() != 0){
            foreach ($delivery_note_shipments as $delivery_note_shipment){
                $shipment = DB::connection('reports')->table('shipments')->find($delivery_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Delivery Note Shipments', 'shipments' => $shipments];
        }else{
            return ['status' => 0, 'success' => 'No Delivery Note Shipments', 'shipments' => FALSE];
        }
    }
    public function fake_status_shipments_undelivered(Request $request){
        $delivery_note_id = $request->input('delivery_note_id');
        $delivery_note_shipments = DB::connection('reports')->table('delivery_note_shipments')->where('delivery_note_id', $delivery_note_id)->where('status', '=', 1)->get();
        $shipments = array();
        if($delivery_note_shipments->count() != 0){
            foreach ($delivery_note_shipments as $delivery_note_shipment){
                $shipment = DB::connection('reports')->table('shipments')->find($delivery_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Delivery Note Shipments', 'shipments' => $shipments];
        }else{
            return ['status' => 0, 'success' => 'No Delivery Note Shipments', 'shipments' => FALSE];
        }
    }

    public function fake_status_shipments(Request $request){
        $delivery_note_id = $request->input('delivery_note_id');
        $delivery_note_shipments = DB::connection('reports')->table('delivery_note_shipments')->where('delivery_note_id', $delivery_note_id)->where('fake_status', '=', 1)->get();

        $shipments = array();
        if($delivery_note_shipments->count() != 0){
            foreach ($delivery_note_shipments as $delivery_note_shipment){
                $shipment = DB::connection('reports')->table('shipments')->find($delivery_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Delivery Note Shipments', 'shipments' => $shipments];
        }else{
            return ['status' => 0, 'success' => 'No Delivery Note Shipments', 'shipments' => FALSE];
        }
    }

    public function debriefing_index() {
        $hubs = DB::connection('reports')->table('cities')->where('hub', 1)->select('id','name')->get();
        $zones = DB::connection('reports')->table('zones')->get();

        return view('admin.reports.debriefing_report')->with(['hubs' => $hubs, 'zones' => $zones]);
    }

    private function debriefing_data($date, $hub, $zone, $export = FALSE) {
        $settings = DB::connection('reports')->table('global_settings')->where('type', 'debriefing_report_arrival_cut_off_time')->first();

        if ($settings) {
            $arrival_cut_off_time = $settings->setting_value;
        }
        else {
            $arrival_cut_off_time = 12;
        }

        $settings = DB::connection('reports')->table('global_settings')->where('type', 'debriefing_report_day_cut_off_time')->first();

        if ($settings) {
            $day_cut_off_time = $settings->setting_value;
        }
        else {
            $day_cut_off_time = 12;
        }

        $hubs = DB::connection('reports')->table('cities')->where('hub', 1)->select('id','name');

        if ($hub) {
            $hubs = $hubs->where('id', '=', $hub);
        }

        if ($zone) {
            $hubs = $hubs->where('zone_id', '=', $zone);
        }

        if ($hubs->exists()) {
            $hubs = $hubs->get();

            if ($date) {
                $from_month = Carbon::parse($date)->subDays(60)->addHour($day_cut_off_time)->toDateTimeString();
                $from = Carbon::parse($date)->addHour($day_cut_off_time)->toDateTimeString();
                $to = Carbon::parse($date)->addDay()->addHour($day_cut_off_time)->subSecond()->toDateTimeString();
            }
            else {
                $from_month = Carbon::today()->subDays(60)->addHour($day_cut_off_time)->toDateTimeString();
                $from = Carbon::today()->addHour($day_cut_off_time)->toDateTimeString();
                $to = Carbon::tomorrow()->addHour($day_cut_off_time)->subSecond()->toDateTimeString();
            }

            $types = ['delivered', 'delivery_unsucessful', 'on_hold', 'status_not_updated', 'confirmation_pending', 'fake_status', 'delivery_note_pending', 'delivery_tomorrow'];

            $counts = array();

            if ($export) {
                $shipments = array();
            }

            foreach ($hubs as $hub) {
                foreach ($types as $type) {
                    $rows = DB::connection('reports')->table('cities')->join('shipments as s', 'cities.id', '=', 's.consignee_city_id');

                    if ($type == 'status_not_updated') {
                        $rows = $rows->join('shipments_journey as sj', function($join) use ($from_month, $to) {
                            $join->on('s.id', '=', 'sj.shipment_id')
                            ->where('sj.id', '=', DB::connection('reports')->raw('(select max(shipments_journey.id) from shipments_journey where shipments_journey.shipment_id = s.id and shipments_journey.verification = 1 and shipments_journey.created_at between "' . $from_month . '" and "' . $to . '")'));
                        });
                    }
                    else if ($type == 'fake_status') {
                        $rows = $rows->join('delivery_note_shipments as dns', 's.id', '=', 'dns.shipment_id')
                        ->join('delivery_notes as dn', function($join) use ($from, $to) {
                            $join->on('dn.id', '=', 'dns.delivery_note_id')
                            ->where('dn.status', '=', 1)
                            ->whereBetween('dn.status_verified_at', [$from, $to]);
                        });
                    }
                    else if ($type == 'delivery_note_pending') {
                        $rows = $rows->join('shipments_journey as sj', function($join) use ($to) {
                            $join->on('s.id', '=', 'sj.shipment_id')
                            ->where('sj.id', '=', DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = s.id and shipments_journey.verification = 1 and shipments_journey.created_at < "' . $to . '")'));
                        });
                    }
                    else {
                        $rows = $rows->join('shipments_journey as sj', function($join) use ($from, $to) {
                            $join->on('s.id', '=', 'sj.shipment_id')
                            ->where('sj.id', '=', DB::connection('reports')->raw('(select max(shipments_journey.id) from shipments_journey where shipments_journey.shipment_id = s.id and shipments_journey.verification = 1 and shipments_journey.created_at between "' . $from . '" and "' . $to . '")'));
                        });
                    }

                    if ($type == 'status_not_updated' || $type == 'delivery_tomorrow') {
                        $rows = $rows->join('user_shipping_infos as usi', 'usi.id', '=', 's.pickup_address_id')
                        ->join('cities as pc', 'usi.city_id', '=', 'pc.id')
                        ->leftjoin('zone_class_cities as zcc', function($join) {
                            $join->on('pc.zone_id', '=', 'zcc.zone_id')
                            ->on('s.consignee_city_id', '=', 'zcc.city_id');
                        });
                    }

                    if ($type == 'delivered') {
                        $rows = $rows->whereIn('sj.shipper_status_id', [14, 30, 36, 37]);
                    }
                    else if ($type == 'delivery_unsucessful') {
                        $rows = $rows->where('sj.shipper_status_id', '=', 8);
                    }
                    else if ($type == 'on_hold') {
                        $rows = $rows->whereIn('sj.shipper_status_id', [9, 10, 11, 15]);
                    }
                    else if ($type == 'status_not_updated') {
                        $rows = $rows->where(function ($query) use ($arrival_cut_off_time, $from) {
                            $query->where('sj.shipper_status_id', '=', 7)
                            ->orWhere(function ($sub_query) use ($arrival_cut_off_time, $from) {
                                $sub_query->where(function ($sub_sub_query) use ($arrival_cut_off_time, $from) {
                                    $sub_sub_query->where('sj.shipper_status_id', '=', 13)
                                    ->orWhere(function ($sub_sub_sub_query) use ($arrival_cut_off_time) {
                                        $sub_sub_sub_query->where(function ($sub_sub_sub_sub_query) {
                                            $sub_sub_sub_sub_query->where('usi.city_id', '=', DB::connection('reports')->raw('s.consignee_city_id'))
                                            ->orWhereNull('zcc.class')
                                            ->orWhereIn('zcc.class', [0, 1]);
                                        })
                                        ->whereIn('sj.shipper_status_id', [2, 4]);
                                    })
                                    ->where(function ($sub_sub_sub_query) use ($arrival_cut_off_time, $from) {
                                        $sub_sub_sub_query->whereRaw('date(`sj`.`created_at`) < date(?)', [$from])
                                        ->orWhere(function ($sub_sub_sub_sub_query) use ($arrival_cut_off_time, $from) {
                                            $sub_sub_sub_sub_query->whereRaw('date(`sj`.`created_at`) = date(?)', [$from])
                                            ->whereRaw('hour(`sj`.`created_at`) < ?', [$arrival_cut_off_time]);
                                        });
                                    });
                                });
                            });
                        });
                    }
                    else if ($type == 'confirmation_pending') {
                        $rows = $rows->where('sj.shipper_status_id', '=', 12);
                    }
                    else if ($type == 'fake_status') {
                        $rows = $rows->where('dns.fake_status', '=', 1);
                    }
                    else if ($type == 'delivery_note_pending') {
                        $rows = $rows->where('sj.shipper_status_id', '=', 5);
                    }
                    else if ($type == 'delivery_tomorrow') {
                        $rows = $rows->where(function ($query) use ($arrival_cut_off_time) {
                            $query->where(function ($sub_query) use ($arrival_cut_off_time) {
                                $sub_query->where(function ($sub_sub_query) {
                                    $sub_sub_query->where(function ($sub_sub_sub_query) {
                                        $sub_sub_sub_query->where('usi.city_id', '=', DB::connection('reports')->raw('s.consignee_city_id'))
                                        ->where('sj.shipper_status_id', 2);
                                    })
                                    ->orWhere(function ($sub_sub_sub_query) {
                                       $sub_sub_sub_query->where('usi.city_id', '!=', DB::connection('reports')->raw('s.consignee_city_id'))
                                       ->where('sj.shipper_status_id', 4);
                                    });
                                })
                                ->where(function ($sub_sub_query) use ($arrival_cut_off_time) {
                                    $sub_sub_query->where(function ($sub_sub_sub_query) use ($arrival_cut_off_time) {
                                        $sub_sub_sub_query->whereRaw('hour(`sj`.`created_at`) >= ?', [$arrival_cut_off_time])
                                        ->orWhereNull('zcc.class')
                                        ->orWhereIn('zcc.class', [2, 3]);
                                    });
                                });
                            })
                            ->orWhere(function ($sub_query) use ($arrival_cut_off_time) {
                                $sub_query->where('sj.shipper_status_id', '=', 13)
                                ->whereRaw('hour(`sj`.`created_at`) >= ?', [$arrival_cut_off_time]);
                            });
                        });
                    }

                    $rows = $rows->select('s.tracking_number')
                    ->where('cities.hub_id', $hub->id);

                    if ($rows->exists()) {
                        $rows = $rows->groupBy('s.id');

                        $rows = $rows->get();

                        $counts[$hub->name][$type] = $rows->count();

                        if ($type != 'fake_status') {
                            if ($type != 'delivery_note_pending' && $type != 'delivery_tomorrow') {
                                if (!isset($counts[$hub->name]['total_1'])) {
                                    $counts[$hub->name]['total_1'] = 0;
                                }

                                $counts[$hub->name]['total_1'] += $counts[$hub->name][$type];
                            }

                            if ($type != 'delivery_tomorrow') {
                                if (!isset($counts[$hub->name]['total_2'])) {
                                    $counts[$hub->name]['total_2'] = 0;
                                }

                                $counts[$hub->name]['total_2'] += $counts[$hub->name][$type];
                            }

                            if (!isset($counts[$hub->name]['grand_total'])) {
                                $counts[$hub->name]['grand_total'] = 0;
                            }

                            $counts[$hub->name]['grand_total'] += $counts[$hub->name][$type];
                        }

                        if ($export) {
                            $shipments[$hub->name][$type] = array();

                            foreach ($rows as $row) {
                                $shipments[$hub->name][$type][] = $row->tracking_number;
                            }
                        }
                    }
                    else {
                        $counts[$hub->name][$type] = 0;
                    }
                }

                foreach ($counts as $hub => $count) {
                    if (isset($count['total_1']) && $count['total_1']) {
                        $counts[$hub]['total_1_ratio'] = round(($count['delivered'] / $count['total_1']) * 100);

                        if (!$export) {
                            $counts[$hub]['total_1_ratio'] .= '%';
                        }
                        else {
                            $counts[$hub]['total_1_ratio'] = ($counts[$hub]['total_1_ratio'] / 100);
                        }
                    }
                    else {
                        $counts[$hub]['total_1'] = 0;

                        if (!$export) {
                            $counts[$hub]['total_1_ratio'] = '0%';
                        }
                        else {
                            $counts[$hub]['total_1_ratio'] = 0;
                        }
                    }

                    if (isset($count['total_2']) && $count['total_2']) {
                        $counts[$hub]['total_2_ratio'] = round(($count['delivered'] / $count['total_2']) * 100);

                        if (!$export) {
                            $counts[$hub]['total_2_ratio'] .= '%';
                        }
                        else {
                            $counts[$hub]['total_2_ratio'] = ($counts[$hub]['total_2_ratio'] / 100);
                        }
                    }
                    else {
                        $counts[$hub]['total_2'] = 0;

                        if (!$export) {
                            $counts[$hub]['total_2_ratio'] = '0%';
                        }
                        else {
                            $counts[$hub]['total_2_ratio'] = 0;
                        }
                    }

                    if (isset($count['grand_total']) && $count['grand_total']) {
                        $counts[$hub]['grand_total_ratio'] = round(($count['delivered'] / $count['grand_total']) * 100);

                        if (!$export) {
                            $counts[$hub]['grand_total_ratio'] .= '%';
                        }
                        else {
                            $counts[$hub]['grand_total_ratio'] = ($counts[$hub]['grand_total_ratio'] / 100);
                        }
                    }
                    else {
                        $counts[$hub]['grand_total'] = 0;

                        if (!$export) {
                            $counts[$hub]['grand_total_ratio'] = '0%';
                        }
                        else {
                            $counts[$hub]['grand_total_ratio'] = 0;
                        }
                    }
                }
            }

            if (!$export) {
                return ['status' => 0, 'success' => 'Shipments Found', 'counts' => $counts];
            }
            else {
                return ['status' => 0, 'success' => 'Shipments Found', 'counts' => $counts, 'shipments' => $shipments];
            }
        }
        else {
            return ['status' => 1, 'error' => 'No Shipments Found'];
        }
    }

    public function debriefing_list(Request $request) {
        $date = $request->get('search_date');
        $hub = $request->get('search_hub');
        $zone = $request->get('search_zone');

        return $this->debriefing_data($date, $hub, $zone);
    }

    public function debriefing_export(Request $request) {
        $date = $request->get('search_date');
        $hub = $request->get('search_hub');
        $zone = $request->get('search_zone');

        $file_name = 'debriefing_report_';

        $file_name .= $date;

        if ($hub) {
            $file_name .= '_' . $hub;
        }

        if ($zone) {
            $file_name .= '_' . $zone;
        }

        $file_name .= '.xlsx';

        $details = array();

        $details[] = ['Hubs', 'Delivered', 'Delivery Unsuccessful', 'On Hold', 'Status Not Updated', 'Confirmation Pending', 'Fake Status', 'Total', 'Ratio', 'Delivery Note Pending', 'Total', 'Ratio', 'Delivery Tomorrow', 'Grand Total', 'Ratio'];

        $result = $this->debriefing_data($date, $hub, $zone, TRUE);

        if ($result['status'] == 0) {
            $types = ['delivered', 'delivery_unsucessful', 'on_hold', 'status_not_updated', 'confirmation_pending', 'fake_status', 'total_1', 'total_1_ratio', 'delivery_note_pending', 'total_2', 'total_2_ratio', 'delivery_tomorrow', 'grand_total', 'grand_total_ratio'];

            $type_names = ['delivered' => 'Delivered', 'delivery_unsucessful' => 'Delivery Unsuccessful', 'on_hold' => 'On Hold', 'status_not_updated' => 'Status Not Updated', 'confirmation_pending' => 'Confirmation Pending', 'fake_status' => 'Fake Status', 'total_1' => 'Total', 'total_1_ratio' => 'Ratio', 'delivery_note_pending' => 'Delivery Note Pending', 'total_2' => 'Total', 'total_2_ratio' => 'Ratio', 'delivery_tomorrow' => 'Delivery Tomorrow', 'grand_total' => 'Grand Total', 'grand_total_ratio' => 'Ratio'];

            foreach ($result['counts'] as $hub => $count) {
                $row = array();

                $row[] = $hub;

                foreach ($types as $type) {
                    if ($count[$type]) {
                        $row[] = $count[$type];
                    }
                    else {
                        $row[] = '0';
                    }
                }

                $details[] = $row;
            }

            $spreadsheet = new Spreadsheet();

            $spreadsheet->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $spreadsheet->getActiveSheet()->getStyle('C')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $spreadsheet->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $spreadsheet->getActiveSheet()->getStyle('E')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $spreadsheet->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $spreadsheet->getActiveSheet()->getStyle('G')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $spreadsheet->getActiveSheet()->getStyle('H')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $spreadsheet->getActiveSheet()->getStyle('I')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
            $spreadsheet->getActiveSheet()->getStyle('J')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $spreadsheet->getActiveSheet()->getStyle('K')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $spreadsheet->getActiveSheet()->getStyle('L')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
            $spreadsheet->getActiveSheet()->getStyle('M')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $spreadsheet->getActiveSheet()->getStyle('N')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $spreadsheet->getActiveSheet()->getStyle('O')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);

            $spreadsheet->getActiveSheet()->getStyle('E')->getFont()->getColor()->setARGB('FFFF0000');
            $spreadsheet->getActiveSheet()->getStyle('G')->getFont()->getColor()->setARGB('FFFF0000');

            $spreadsheet->getActiveSheet()->setTitle('Overall')->fromArray($details, NULL);

            $types = array();

            foreach ($result['shipments'] as $hub => $types) {
                $details = array();

                if (count($types) > 1) {
                    foreach ($types as $type => $tracking_numbers) {
                        $detail = array();

                        $detail[] = $type_names[$type];

                        foreach ($tracking_numbers as $tracking_number) {
                            $detail[] = $tracking_number;
                        }

                        $details[] = $detail;
                    }

                    $details = array_map(null, ...$details);
                }
                else {
                    foreach ($types as $type => $tracking_numbers) {
                        $details[] = [$type_names[$type]];

                        foreach ($tracking_numbers as $tracking_number) {
                            $details[] = [$tracking_number];
                        }
                    }
                }

                $spreadsheet->createSheet()->setTitle($hub);

                $spreadsheet->setActiveSheetIndexByName($hub);

                for ($counter = 1; $counter <= count($types); $counter++) {
                    $column_name = Coordinate::stringFromColumnIndex($counter);

                    $spreadsheet->getActiveSheet()->getStyle($column_name)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);

                    $spreadsheet->getActiveSheet()->getColumnDimension($column_name)->setWidth(15);
                }

                $spreadsheet->getActiveSheet()->setTitle($hub)->fromArray($details);
            }

            $spreadsheet->setActiveSheetIndex(0);

            $writer = new Xlsx($spreadsheet);
        }
        else {
            $spreadsheet = new Spreadsheet();
            $spreadsheet->getActiveSheet()->setTitle('Overall')->fromArray($details);

            $writer = new Xlsx($spreadsheet);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file_name . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }
    public function cargo_returns_shipment_index(){

        $cities = DB::connection('reports')->table('cities')->select('id','name')->where('hub',1)->get();
        return view('admin.reports.cargo_returns_shipment_report')->with('cities', $cities);
    }
    public function cargo_returns_shipment_list(Request $request){
        $cargo_returns_Shipment = DB::connection('reports')->table('shipments')->join('shipments_journey as sj', function ($join) {
            $join->on('sj.shipment_id' , '=', 'shipments.id')
                    ->where('sj.id' , '=', DB::connection('reports')->raw('(select max(id) from shipments_journey where shipment_id = shipments.id and shipments_journey.shipper_status_id in (20, 30, 37))'));
            })
            ->leftjoin('shipments_journey as sjc', function ($join) {
                $join->on('sjc.shipment_id' , '=', 'shipments.id')
                    ->where('sjc.id' , '=', DB::connection('reports')->raw('(select max(id) from shipments_journey where shipment_id = shipments.id and shipments_journey.shipper_status_id in (21, 26, 32))'));
            })
            ->leftjoin('cargo_consignments as cc', 'cc.id', '=', 'sjc.reference_1_id')
            ->leftjoin('cargo_consignment_shipments as ccs', function ($join) {
                $join->on('ccs.shipment_id', '=', 'shipments.id')
                ->where('ccs.cargo_consignment_id', '=', 'cc.id');
            })
            ->join('user_shipping_infos as usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities as shipment_origin_city', 'shipment_origin_city.id', '=', 'usi.city_id')
            ->join('cities as shipment_origin_hub', 'shipment_origin_hub.id', '=', 'shipment_origin_city.hub_id')
            ->join('cities as shipment_destination_city', function($join) {
                $join->on('shipments.consignee_city_id', '=', 'shipment_destination_city.id')
                    ->on('shipment_origin_city.hub_id', '!=', 'shipment_destination_city.hub_id');
            })
            ->join('cities as shipment_destination_hub', 'shipment_destination_hub.id', '=', 'shipment_destination_city.hub_id')
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->select('shipments.tracking_number as tracking_number', 'ss.name as status','ss.id as status_id', 'sj.created_at as return_confirm_date', 'sjc.created_at as dispatching_aging', 'cc.id as cargo_no', 'cc.created_at as cargo_creation_date', 'shipment_origin_city.name as shipment_origin_city_name', 'shipment_origin_hub.name as shipment_origin_hub_name', 'shipment_destination_city.name as shipment_destination_city_name', 'shipment_destination_hub.name as shipment_destination_hub_name')
            ->whereIn('shipments.shipper_status_id', [20, 21, 26, 30, 32, 37]);
        $cargo_returns_Shipment = Datatables::of($cargo_returns_Shipment)
            ->editColumn('dispatching_aging', function($shipments){
                $from = Carbon::parse($shipments->dispatching_aging);
                $days = Carbon::now()->diffInDays($from);
                if($days == 0){
                    return "-";
                }else{
                    return $days;
                }
            })
            ->addColumn('cargo_id_padded_link', function ($shipments) {
                if($shipments->cargo_no != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($shipments->cargo_no, 6, '0', STR_PAD_LEFT) . '</span></button>';
                }
                else{
                    return '-';
                }
            })
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('return_confirm_aging', function($shipments){
                $from = Carbon::parse($shipments->return_confirm_date);
                $days = Carbon::now()->diffInDays($from);
                if($days == 0){
                    return "-";
                }else{
                    return $days;
                }
            });

        if ($origin = $request->get('search_origin')) {
            $cargo_returns_Shipment->where('shipment_destination_hub.id', '=', $origin);
        }


        if ($destination = $request->get('search_destination')) {
            $cargo_returns_Shipment->where('shipment_origin_hub.id', '=', $destination);
        }

        if($status = $request->get('search_status')){
            $cargo_returns_Shipment->where('ss.id', '=', $status);
        }

        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');

            $cargo_returns_Shipment->whereBetween('sj.created_at', [$from, $to]);
        }
        return $cargo_returns_Shipment->make(true);
    }


    public function return_reattempt_ratio_index(){
        $cities = DB::connection('reports')->table('cities')->where('status', 1)->get();
        return view('admin.reports.return_reattempt_ratio')->with(['cities' => $cities]);
    }

    public function return_reattempt_ratio_list(Request $request){
        $shipments = DB::connection('reports')->table('return_reattempt_ratios')->join('shipments','shipments.id','=','return_reattempt_ratios.shipment_id')
            ->join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id','=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipments_journey as journey', function ($join) {
                $join->on('journey.shipment_id', '=', 'shipments.id')
                    ->where('journey.id', '=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->select(['shipments.id as shId','shipments.tracking_number','shipments.tracking_number as tracking_number_link','u.name as shipper','ss.name as current_status','bt.booking_type as service_type','sj.created_at as arrival','oc.name as origin','dc.name as destination','h.name as hub','shipments.amount','journey.created_at as current_status_date', 'shipments.booking_type_id', 'return_reattempt_ratios.return_confirm_date','return_reattempt_ratios.created_at as reattempt_date', 'u.id as account_no']);
        if (session('role_id') != 1) {
            $shipments = $shipments->whereIn('dc.hub_id', session('hubs'));
        }

        $datatable = Datatables::of($shipments)
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->editColumn('account_no', function ($shipments) {
                return str_pad($shipments->account_no, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper .' (' . $shipment->poc . ')';
                }
                else {
                    return $shipment->shipper;
                }
            })
            ->addColumn('reversion_aging',function ($shipments){

                $days = Carbon::parse($shipments->return_confirm_date)->diffInDays($shipments->reattempt_date);
                if($days == 0){
                    return "-";
                }else{
                    return $days;
                }
            })
            ->addColumn('aging_current_status',function ($shipments){

                $days = Carbon::parse($shipments->reattempt_date)->diffInDays($shipments->current_status_date);
                if($days == 0){
                    return "-";
                }else{
                    return $days;
                }
            });
        if ($city = $request->get('search_city')) {
            $datatable->where('dc.id', '=', $city);
        }

        if ($request->get('search_from') && $request->get('search_to')) {
            $from = $request->get('search_from');
            $to = $request->get('search_to');
            $datatable->whereBetween('return_confirm_date', [$from,$to]);
        }

        return $datatable->make(true);
    }

    public function multiple_payment_report_index(){
        return view('admin.reports.multiple_payment_report');
    }
    public function multiple_payment_report_list(Request $request){
        $payments = DB::connection('reports')->table('done_payment_shipments')->join('shipments as s','s.id', '=', 'done_payment_shipments.shipment_id')->select(['s.tracking_number as tracking_number', 's.actual_weight as actual_weight', 's.cash_handling_charges as cash_handling_charges','s.insurance_charges as insurance_charges','s.return_charges as return_charges','s.fuel_surcharge as fuel_surcharge','s.replacement_charges as replacement_charges','s.packaging_material_charges as packaging_material_charges', 'done_payment_shipments.done_payment_id as payment_id', 'done_payment_shipments.gst as gst', 'done_payment_shipments.amount as amount', 'done_payment_shipments.payable as total_payable', 'done_payment_shipments.type as status', 's.nsa_osa_charges as nsa_osa_charges']);
        $datatable = Datatables::of($payments)
            ->addColumn('id_padded', function ($shipments) {
                return str_pad($shipments->payment_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('payment_id_link', function($shipments) {
                return '<button class="btn btn-sm btn-outline-info align-middle"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($shipments->payment_id, 6, '0', STR_PAD_LEFT) . '</span></button>';
            })
            ->editColumn('cash_handling_charges', function ($shipments){
                return number_format($shipments->cash_handling_charges, 2);
            })
            ->editColumn('insurance_charges', function ($shipments){
                return number_format($shipments->insurance_charges, 2);
            })
            ->editColumn('return_charges', function ($shipments){
                return number_format($shipments->return_charges, 2);
            })
            ->editColumn('fuel_surcharge', function ($shipments){
                return number_format($shipments->fuel_surcharge, 2);
            })
            ->editColumn('replacement_charges', function ($shipments){
                return number_format($shipments->replacement_charges, 2);
            })
            ->editColumn('nsa_osa_charges', function($shipment){
                return number_format($shipment->nsa_osa_charges, 2);
            })
            ->editColumn('packaging_material_charges', function ($shipments){
                return number_format($shipments->packaging_material_charges, 2);
            })
            ->editColumn('gst', function ($shipments){
                return number_format($shipments->gst, 2);
            })
            ->editColumn('amount', function ($shipments){
                return number_format($shipments->amount, 2);
            })
            ->editColumn('total_payable', function ($shipments){
                return number_format($shipments->total_payable, 2);
            })
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('status', function($shipments){
               if($shipments->status == 0){
                   return 'Delivered';
               }
               elseif ($shipments->status == 1){
                   return 'Returned';
               }
               else{
                   return 'Adjusted';
               }

            });
        if ($tracking_number = $request->get('tracking_number')) {
            $datatable->where('s.tracking_number', '=', $tracking_number);
        }
        return $datatable->make(true);
    }
    public function revenue_index(){
        $shippers = DB::connection('reports')->table('users')->whereIn('status',[3,4])->select('id','name')->get();
        $cities = DB::connection('reports')->table('cities')->select('id','name')->get();
        $hubs = DB::connection('reports')->table('cities')->where('hub',1)->select('id','name')->get();
        $statuses = DB::connection('reports')->table('shipment_status')->whereNotIn('id',[1,17])->get();
        return view('admin.reports.revenue')->with(['shippers'=>$shippers,'cities'=>$cities,'hubs'=>$hubs,'statuses'=>$statuses]);
    }
    public function revenue_list(Request $request){

        $sales = DB::connection('reports')->table('shipments')->join('users as u','u.id','=','shipments.user_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->leftjoin('zones as z', 'z.id', '=', 'oc.zone_id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->leftjoin('zone_class_cities as zcc', function($join){
                $join->on('z.id', '=', 'zcc.zone_id')
                    ->on('dc.id', '=', 'zcc.city_id')
                    ->on('zone_classification_id', '=', DB::connection('reports')->raw('IF (shipments.shipping_mode_id IN (1, 4), 1, 2)'));
            })
            ->join('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=' , 'sps.id')
            ->leftjoin('delivery_note_shipments as ds',function($join){
                $join->on('ds.shipment_id','=','shipments.id')
                    ->where('ds.delivery_note_id','=',
                        DB::connection('reports')->raw('(select max(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = shipments.id and delivery_note_shipments.status > 3 and  delivery_note_shipments.status != 8)'));
            })
            ->leftjoin('delivery_note_station_deposit_notes as dnsdn', 'ds.delivery_note_id', '=', 'dnsdn.delivery_note_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id','=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('pending_payment_shipments as pps', function ($join) {
                $join->on('pps.shipment_id', '=', 'shipments.id')
                    ->where('pps.id','=',
                        DB::connection('reports')->raw('(select max(id) from pending_payment_shipments where pending_payment_shipments.shipment_id = shipments.id)'));
            })
            ->leftJoin('done_payment_shipments as dps', function ($join) {
                $join->on('dps.shipment_id', '=', 'shipments.id')
                    ->where('dps.id','=',
                        DB::connection('reports')->raw('(select max(id) from done_payment_shipments where done_payment_shipments.shipment_id = shipments.id)'));
            })
            ->leftJoin('pending_invoice_shipments as pis', function ($join) {
                $join->on('pis.shipment_id', '=', 'shipments.id')
                    ->where('pis.id','=',
                        DB::connection('reports')->raw('(select max(id) from pending_invoice_shipments where pending_invoice_shipments.shipment_id = shipments.id)'));
            })
            ->leftJoin('invoice_shipments as is', function ($join) {
                $join->on('is.shipment_id', '=', 'shipments.id')
                    ->where('is.id','=',
                        DB::connection('reports')->raw('(select max(id) from invoice_shipments where invoice_shipments.shipment_id = shipments.id)'));
            })
            ->leftJoin('shipments_journey as dr', function ($join) {
                $join->on('dr.shipment_id', '=', 'shipments.id')
                    ->where('dr.id','=',
                        DB::connection('reports')->raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,20,30,36,37))'));
            })
            ->select('shipments.tracking_number','shipments.order_id as order_id','shipments.tracking_number as tracking_number_link','u.id as account_no','u.name as shipper','ss.name as current_status','bt.booking_type as service_type','sj.created_at as arrival_date','oc.name as origin','dc.name as destination','h.name as hub','shipments.amount as s_collection_amount','sps.name as payment_status','pps.amount as p_collection_amount','shipments.actual_weight','shipments.weight_charges','shipments.cash_handling_charges','shipments.insurance_charges','shipments.return_charges','shipments.replacement_charges','shipments.fuel_surcharge','shipments.try_and_buy_charges','shipments.packaging_material_charges','pps.gst as p_gst','pps.charges as p_total_charges','pps.payable as p_net_payable','dps.amount as d_collection_amount','dps.gst as d_gst','dps.charges as d_total_charges','dps.payable as d_net_payable', 'sm.mode as shipping_mode','shipments.chargeable_weight','dr.created_at as delivered_or_returned','z.name as zone','zcc.class', 'oc.id as origin_city_id', 'dc.id as destination_city_id', 'dnsdn.station_deposit_note_id as sdn_id', 'dps.done_payment_id as payment_id', 'shipments.booking_type_id', 'usi.poc', 'shipments.shipper_status_id as shipment_status', 'shipments.nsa_osa_charges', 'u.account_type_id as account_type_id', 'pis.gst as pis_gst', 'is.gst as is_gst')
            ->whereNotIn('shipments.shipper_status_id',[1,17]);
//        if (!$request->get('search_date_from') && !$request->get('search_date_to')) {
//            $now = Carbon::now();
//            $yesterday = Carbon::now()->subDays(3);
//            $sales = $sales->whereBetween('sj.created_at', [$yesterday,$now]);
//        }

        if (session('role_id') != 1) {
            if (session('department_id') == 7 && session('role_id') != 4) {
                $sales = $sales->whereIn('u.id', session('tagged_shippers'));
            }
            else {
                $sales = $sales->whereIn('dc.hub_id', session('hubs'));
            }
        }

        $datatable = Datatables::of($sales)
            ->editColumn('insurance_charges', function($shipment){
                return number_format($shipment->insurance_charges, 2);
            })
            ->editColumn('cash_handling_charges', function($shipment){
                if($shipment->shipment_status == 20 || $shipment->shipment_status == 21 || $shipment->shipment_status == 22 || $shipment->shipment_status == 23 || $shipment->shipment_status == 23 || $shipment->shipment_status == 25){
                    return "-";
                }
                else{
                    if($shipment->cash_handling_charges != null){
                        return $shipment->cash_handling_charges;
                    }
                    else{
                        return "-";
                    }
                }
            })
            ->editColumn('account_no', function ($shipments) {
                return str_pad($shipments->account_no, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('return_charges', function($shipment){
                return number_format($shipment->return_charges, 2);
            })
            ->editColumn('weight_charges', function($shipment){
                return number_format($shipment->weight_charges, 2);
            })
            ->editColumn('fuel_surcharge', function($shipment){
                return number_format($shipment->fuel_surcharge, 2);
            })
            ->editColumn('replacement_charges', function($shipment){
                return number_format($shipment->replacement_charges, 2);
            })
            ->editColumn('try_and_buy_charges', function($shipment){
                return number_format($shipment->try_and_buy_charges, 2);
            })
            ->editColumn('nsa_osa_charges', function($shipment){
                return number_format($shipment->nsa_osa_charges, 2);
            })
            ->editColumn('packaging_material_charges', function($shipment){
                return number_format($shipment->packaging_material_charges, 2);
            })
            ->editColumn('p_total_charges', function($shipment){
                return number_format($shipment->p_total_charges, 2);
            })
            ->editColumn('d_total_charges', function($shipment){
                return number_format($shipment->d_total_charges, 2);
            })
            ->editColumn('p_net_payable', function($shipment){
                return number_format($shipment->p_net_payable, 2);
            })
            ->editColumn('d_net_payable', function($shipment){
                return number_format($shipment->d_net_payable, 2);
            })
            ->editColumn('d_gst', function($shipment){
                return number_format($shipment->d_gst, 2);
            })
            ->editColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('s_collection_amount', function($shipment){
                return number_format($shipment->s_collection_amount, 2);
            })
            ->editColumn('d_collection_amount', function($shipment){
                return number_format($shipment->d_collection_amount, 2);
            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper .' (' . $shipment->poc . ')';
                }
                else {
                    return $shipment->shipper;
                }
            })
            ->editColumn('p_collection_amount',function($sale){
                $amount = '';
                if($sale->p_collection_amount != null){
                    $amount = $sale->p_collection_amount;
                }else if($sale->d_collection_amount != null){
                    $amount = $sale->d_collection_amount;
                }else{
                    $amount = $sale->s_collection_amount;
                }
                return number_format($amount);
            })
            ->editColumn('p_gst',function($sale){
                $gst = '';
                if($sale->account_type_id == 1){
                    if($sale->p_gst != null){
                        $gst = $sale->p_gst;
                    }else if($sale->d_gst != null){
                        $gst = $sale->d_gst;
                    }
                }
                else{
                    if($sale->pis_gst != null){
                        $gst = $sale->pis_gst;
                    }else if($sale->is_gst != null){
                        $gst = $sale->is_gst;
                    }
                }
                return number_format((float)$gst, 2);
            })
            ->editColumn('p_total_charges',function($sale){
                $total = '';
                if($sale->p_total_charges != null){
                    $total = $sale->p_total_charges;
                }else if($sale->d_total_charges != null){
                    $total = $sale->d_total_charges;
                }
                return number_format((float)$total, 2);
            })
            ->addColumn('estimated_charges',function($sale){
                $estimated = '';
                $estimated = (($sale->weight_charges != null)? $sale->weight_charges:0) + (($sale->cash_handling_charges != null)? $sale->cash_handling_charges:0) + (($sale->insurance_charges != null)? $sale->insurance_charges:0) + (($sale->insurance_charges != null)? $sale->insurance_charges:0) + (($sale->return_charges != null)? $sale->return_charges:0) + (($sale->replacement_charges != null)? $sale->replacement_charges:0) + (($sale->fuel_surcharge != null)? $sale->fuel_surcharge:0) + (($sale->try_and_buy_charges != null)? $sale->try_and_buy_charges:0) + (($sale->packaging_material_charges != null)? $sale->packaging_material_charges:0);
                return number_format((float)$estimated, 2);
            })
            ->editColumn('p_net_payable',function($sale){
                $payable = '';
                if($sale->p_net_payable != null){
                    $payable = $sale->p_net_payable;
                }else if($sale->d_net_payable != null){
                    $payable = $sale->d_net_payable;
                }
                return number_format((float)$payable, 2);
            })
            ->addColumn('class',function($sale){
                $class = '';

                if ($sale->origin_city_id != $sale->destination_city_id) {
                    switch ($sale->class) {
                        case 0:
                            $class = 'Class A';
                            break;
                        case 1:
                            $class = 'Class B';
                            break;
                        case 2:
                            $class = 'Class C';
                            break;
                        case 3:
                            $class = 'Class D';
                            break;
                    }
                }

                return $class;
            });

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
            $datatable->whereBetween('sj.created_at', [$from,$to]);
        }
        return $datatable->make(true);
    }
    public function gst_index(){
        return view('admin.reports.gst_report');
    }

    public function gst_list(Request $request){
        $gst = DB::connection('reports')->table('done_payments')->leftjoin('done_payment_shipments as dps','dps.done_payment_id', '=', 'done_payments.id')
            ->leftjoin('users as u', 'done_payments.user_id', '=', 'u.id')
            ->select('u.id as account_no', 'u.name as user_name', 'u.ntn_no as ntn_number', DB::connection('reports')->raw('SUM(dps.charges) as w_o_gst'), DB::connection('reports')->raw('SUM(dps.gst) as gst'), DB::connection('reports')->raw('SUM(dps.payable) as total_charges'))
        ->groupBy('done_payments.user_id');

        $datatables = Datatables::of($gst)
            ->editColumn('account_no', function ($gst) {
                return str_pad($gst->account_no, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('ntn_number', function ($gst) {
                if($gst->ntn_number) {
                    return $gst->ntn_number;
                }
                else{
                    return "-";
                }
            })
            ->editColumn('total_charges', function ($gst) {
                return number_format($gst->total_charges, 2);
            })
            ->editColumn('w_o_gst', function ($gst) {
                return number_format($gst->w_o_gst, 2);
            })
            ->editColumn('gst', function ($gst) {
                return number_format($gst->gst, 2);
            });
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatables->whereBetween('dps.created_at', [$from,$to]);
        }
        return $datatables->make(true);
    }

    public function crm_index(){
        $shippers = DB::connection('reports')->table('users')->where('status', 3)->select('id','name')->get();
        $cities = DB::connection('reports')->table('cities')->select('id','name')->get();
        $hubs = DB::connection('reports')->table('cities')->where('hub',1)->select('id','name')->get();
        $agents = DB::connection('reports')->table('admin_roles')->leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')
            ->where('admin_roles.department_id',3)->get();
        $case_natures = DB::connection('reports')->table('crm_request_case_nature')->select('id', 'name')->get();
        return view('admin.reports.crm_report')->with(['shippers'=>$shippers,'cities'=>$cities,'hubs'=>$hubs,'agents'=>$agents,'case_natures'=>$case_natures]);
    }

    public function crm_list(Request $request){
        $crm = DB::connection('reports')->table('crm_requests')->leftjoin('shipments as s','s.id','=','crm_requests.shipment_id')
            ->leftjoin('crm_request_statuses as crs', 'crs.id' , '=', 'crm_requests.status_id')
            ->leftjoin('crm_request_case_nature as crcn', 'crcn.id', '=', 'crm_requests.case_nature_id')
            ->leftjoin('crm_request_case_nature_types as crcnt', 'crcnt.id', '=', 'crm_requests.case_nature_type_id')
            ->leftjoin('users as u', 'u.id', '=', 'crm_requests.shipper_id')
            ->leftjoin('user_shipping_infos AS usi', 'usi.id', '=', 's.pickup_address_id')
            ->leftjoin('cities as oc', 'oc.id', '=', 'usi.city_id')
            ->leftjoin('cities as dc', 'dc.id', '=', 's.consignee_city_id')
            ->leftjoin('cities as h' ,'h.id', '=' , 'dc.hub_id')
            ->leftjoin('crm_request_channels as crc' ,'crc.id', '=' , 'crm_requests.channel_id')
            ->leftjoin('admins as a' ,'a.id', '=' , 'crm_requests.agent_id')
            ->leftJoin('admins as al', function ($join) {
                $join->on('al.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(0));
            })
            ->leftJoin('users as us', function ($join) {
                $join->on('us.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(1));
            })
            ->leftJoin('substitute_users as su', function ($join) {
                $join->on('su.id', '=', 'crm_requests.launched_by_id')
                    ->where('crm_requests.launched_by', '=', DB::raw(2));
            })
            ->leftjoin('crm_request_agent_histories as crah', function ($join){
                $join->on('crah.crm_request_id', '=', 'crm_requests.id')
                    ->where('crah.id', '=', DB::connection('reports')->raw('(select max(id) from crm_request_agent_histories where crm_request_id = crm_requests.id and agent_id = crm_requests.agent_id)'));
            })
            ->leftjoin('crm_request_status_histories as crshv', function ($join){
                $join->on('crshv.crm_request_id', '=', 'crm_requests.id')
                    ->where('crshv.id', '=', DB::connection('reports')->raw('(select min(id) from crm_request_status_histories where crm_request_id = crm_requests.id and status_id = 6)'));
            })
            ->leftjoin('crm_request_status_histories as crshiv', function ($join){
                $join->on('crshiv.crm_request_id', '=', 'crm_requests.id')
                    ->where('crshiv.id', '=', DB::connection('reports')->raw('(select min(id) from crm_request_status_histories where crm_request_id = crm_requests.id and status_id = 7)'));
            })
            ->leftjoin('crm_request_status_histories as crshr', function ($join){
                $join->on('crshr.crm_request_id', '=', 'crm_requests.id')
                    ->where('crshr.id', '=', DB::connection('reports')->raw('(select max(id) from crm_request_status_histories where crm_request_id = crm_requests.id and status_id = 3)'));
            })
            ->leftjoin('crm_request_status_histories as crshc', function ($join){
                $join->on('crshc.crm_request_id', '=', 'crm_requests.id')
                    ->where('crshc.id', '=', DB::connection('reports')->raw('(select max(id) from crm_request_status_histories where crm_request_id = crm_requests.id and status_id = 4)'));
            })
            ->select('crm_requests.id as request_number', 's.tracking_number as tracking_number','crcn.name as case_nature','crcnt.type as case_nature_type', 'crm_requests.description as description', 'u.name as shipper_name', 'oc.name as origin', 'dc.name as destination', 'h.name as hub', 'crc.channel as channel', 'a.name as agent', 'al.name as name', 'us.name as shipper', 'su.name as sub_shipper', 'crm_requests.launched_by as launched_by_type', 'crm_requests.created_at as launched_date', 'crah.created_at as assigned_date', 'crshv.created_at as valid_date', 'crshiv.created_at as invalid_date', 'crshr.created_at as resolved_date', 'crshc.created_at as closed_date', 'crm_requests.status_id as current_status_id', 'crs.name as request_status')
        ->groupBy('crm_requests.id');
        $datatable = Datatables::of($crm)
            ->editColumn('tracking_number_link', function ($crm_request) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$crm_request->tracking_number' class='tracking' target='_blank'>$crm_request->tracking_number</a></u>";
            })
            ->editColumn('valid_invalid_status', function($crm_request){
                if($crm_request->valid_date != null) {
                    return 'Valid';
                }
                else if ($crm_request->invalid_date != null){
                    return 'Invalid';
                }
                else{
                    return '-';
                }
            })
            ->editColumn('valid_invalid_date', function($crm_request){
                if($crm_request->valid_date != null) {
                    return $crm_request->valid_date;
                }
                else if ($crm_request->invalid_date != null){
                    return $crm_request->invalid_date;
                }
                else{
                    return '-';
                }
            })
            ->editColumn('request_number', function ($crm_request) {
                return str_pad($crm_request->request_number, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('launched_by_name', function ($requests){
                $name = '';
                if($requests->launched_by_type == 0){
                    $name = $requests->name;
                }
                else if($requests->launched_by_type == 1){
                    $name = $requests->shipper;
                }else{
                    $name = $requests->sub_shipper;
                }
                return $name;
            })
            ->filterColumn('launched_by_name', function($query, $keyword) {
                $keyword = strtolower($keyword);

                if ($keyword != '') {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('crm_requests.launched_by', '=', 0)
                            ->where('a.name', 'like', '%' . $keyword . '%');
                    })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('crm_requests.launched_by', '=', 1)
                                ->where('us.name', 'like', '%' . $keyword . '%');
                        })
                        ->orWhere(function ($sub_query) use ($keyword) {
                            $sub_query->where('crm_requests.launched_by', '=', 2)
                                ->where('su.name', 'like', '%' . $keyword . '%');
                        });
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('launched_by_name', DB::connection('reports')->raw('IF (crm_requests.launched_by = 0, a.name, IF (crm_requests.launched_by = 1, us.name, IF (crm_requests.launched_by = 2, su.name, "")))') . ' $1')

            ->editColumn('launched_by_type', function($requests){
                if($requests->launched_by_type == 0) {
                    return 'Admin';
                }
                else if($requests->launched_by_type == 1) {
                    return 'Shipper';
                }
                else{
                    return 'Shipper Substitute User';
                }
            })
            ->editColumn('closed_date', function($requests){
                if($requests->current_status_id == 4) {
                    return $requests->closed_date;
                }
                else{
                    return '-';
                }
            })
            ->editColumn('resolved_date', function($requests){
                if($requests->current_status_id == 3 || $requests->current_status_id == 4) {
                    return $requests->resolved_date;
                }
                else{
                    return '-';
                }
            });
        if($tracking = $request->get('search_tracking_no')){
            $datatable->where('s.tracking_number', '=', $tracking);
        }
        if($shipper = $request->get('search_shipper')){
            $datatable->where('u.id', '=', $shipper);
        }
//        if($origin = $request->get('search_origin')){
//            $datatable->where('oc.id', '=', $origin);
//        }
        if($destination = $request->get('search_destination')){
            $datatable->where('dc.id', '=', $destination);
        }
        if($hub = $request->get('search_hub')){
            $datatable->where('h.id', '=', $hub);
        }
        if($case_nature = $request->get('search_case_nature')){
            $datatable->where('crcn.id', '=', $case_nature);
        }
        if($agent = $request->get('search_agent')){
            $datatable->where('a.id', '=', $agent);
        }
        if ($request->get('search_from') && $request->get('search_to')) {
            $from = $request->get('search_from');
            $to = $request->get('search_to');
            $datatable->whereBetween('crm_requests.created_at', [$from,$to]);
        }

        return $datatable->make(true);
    }

    public function summary_index(Request $request){
        $today = Carbon::now()->endOfDay();
        $thirtyDays = Carbon::now()->subDays(30)->startOfDay();
        $shippers = DB::connection('reports')->table('users')->where('status','>=',3)->get();
        $hubs = DB::connection('reports')->table('cities')->select('id','name')->where('hub',1)->get();

        return view('admin.reports.summary')->with(['hubs'=>$hubs,'shippers'=>$shippers,'today' => $today, 'thirtyday' => $thirtyDays]);
    }

    public function summary_data(Request $request){
        $stats = array();
        $shipper = $request->shipper;
        $from = $request->from_date;
        $to = $request->to_date;
        $origin = $request->origin;
        $destination = $request->destination;
        if($from == null || $to == null){
            $toDays = Carbon::now()->endOfDay();
            $fromDays = Carbon::now()->subDays(30)->startOfDay();
        }else{
            $fromDays = $from;
            $toDays = $to;
        }

        $stats['total'] = DB::connection('reports')->table('shipments')->whereBetween('created_at',[$fromDays,$toDays])->where('user_id', $shipper);
        $stats['booked'] = DB::connection('reports')->table('shipments')->where('shipper_status_id',1)->whereBetween('created_at',[$fromDays,$toDays])->where('user_id', $shipper);
        $stats['canceled'] = DB::connection('reports')->table('shipments')->where('shipper_status_id',17)->whereBetween('created_at',[$fromDays,$toDays])->where('user_id', $shipper);
        $stats['received'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id',[2,3,4])->whereBetween('created_at',[$fromDays,$toDays])->where('user_id', $shipper);
        $stats['delivered'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id',[14,16, 30, 36,37,39,40,41,47])->whereBetween('created_at',[$fromDays,$toDays])->where('user_id', $shipper);
        $stats['return'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id',[20,21,22,23,24,25,26,27,28,29,31,32,33,34,35,38,42,43,44,45,46,50])->whereBetween('created_at',[$fromDays,$toDays])->where('user_id', $shipper);
        $stats['in_process'] = DB::connection('reports')->table('shipments')->whereIn('shipper_status_id',[5,6,7,8,9,10,11,12,13,15,18,19,49,52])->whereBetween('created_at',[$fromDays,$toDays])->where('user_id', $shipper);
        if ($origin) {
            $stats['total'] = $stats['total']->whereExists(function($query) use ($origin) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($origin) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $origin);
                    });
            });
            $stats['booked'] = $stats['booked']->whereExists(function($query) use ($origin) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($origin) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $origin);
                    });
            });
            $stats['canceled'] = $stats['canceled']->whereExists(function($query) use ($origin) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($origin) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $origin);
                    });
            });
            $stats['received'] = $stats['received']->whereExists(function($query) use ($origin) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($origin) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $origin);
                    });
            });
            $stats['delivered'] = $stats['delivered']->whereExists(function($query) use ($origin) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($origin) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $origin);
                    });
            });
            $stats['return'] = $stats['return']->whereExists(function($query) use ($origin) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($origin) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $origin);
                    });
            });
            $stats['in_process'] = $stats['in_process']->whereExists(function($query) use ($origin) {
                $query->from('user_shipping_infos')
                    ->where('shipments.pickup_address_id', '=', DB::raw('`user_shipping_infos`.`id`'))
                    ->whereExists(function ($sub_query) use ($origin) {
                        $sub_query->from('cities')
                            ->where('user_shipping_infos.city_id', '=', DB::raw('`cities`.`id`'))
                            ->where('cities.id', $origin);
                    });
            });
        }

        if ($destination) {
            $stats['total'] = $stats['total']->where('consignee_city_id', $destination);
            $stats['booked'] = $stats['booked']->where('consignee_city_id', $destination);
            $stats['received'] = $stats['received']->where('consignee_city_id', $destination);
            $stats['canceled'] = $stats['canceled']->where('consignee_city_id', $destination);
            $stats['delivered'] = $stats['delivered']->where('consignee_city_id', $destination);
            $stats['return'] = $stats['return']->where('consignee_city_id', $destination);
            $stats['in_process'] = $stats['in_process']->where('consignee_city_id', $destination);
        }
        $stats['total'] = number_format($stats['total']->count());
        $stats['booked'] = number_format($stats['booked']->count());
        $stats['canceled'] = number_format($stats['canceled']->count());
        $stats['received'] = number_format($stats['received']->count());
        $stats['delivered'] = number_format($stats['delivered']->count());
        $stats['return'] = number_format($stats['return']->count());
        $stats['in_process'] = number_format($stats['in_process']->count());


        return response()->json(['status' => 1, 'stats' => $stats]);

    }
    public function summary_list(Request $request){
        $shipments = DB::connection('reports')->table('shipments')->join('users as u','u.id','=','shipments.user_id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=' , 'sps.id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftjoin('shipment_items as si', function ($join) {
                $join->on('si.shipment_id', '=', 'shipments.id')
                    ->where('si.type','=',0);
            })
            ->select(['shipments.id as shipment_id','shipments.order_id','shipments.tracking_number','shipments.amount as collection_amount','ss.name as current_status','sps.name as payment_status','bt.booking_type as service_type','sj.created_at as arrival_date','oc.name as origin','dc.name as destination','u.name as shipper','shipments.consignee_name','shipments.consignee_phone_number_1 as phone1','shipments.consignee_phone_number_2 as phone2','shipments.consignee_address','shipments.created_at as booking_date']);
        if( $request->get('search_shipper')){
            $shipments->where('shipments.user_id', '=',$request->get('search_shipper'));
        }else{
            $shipments->where('shipments.user_id', '=', null);
        }


        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $shipments = $shipments->whereBetween('shipments.created_at', [$from,$to]);
        }

        $datatable = Datatables::of($shipments)
            ->addColumn('tracking_number_link', function ($shipments) {
                $route = route('cod.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('phone',function ($shipments){
                return $shipments->phone1."<br>".$shipments->phone2;
            })
            ->filterColumn('phone', function ($query, $keyword) {
                $keyword = strtolower($keyword);

                $keyword = str_replace('-', '', $keyword);

                if ($keyword != '') {
                    $query->where(function ($sub_query) use ($keyword) {
                        $sub_query->where('shipments.consignee_phone_number_1', 'like', '%' . $keyword . '%')
                            ->orWhere('shipments.consignee_phone_number_2', 'like', '%' . $keyword . '%');
                    });
                }

                else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('phone', 'shipments.consignee_phone_number_1 $1, shipments.consignee_phone_number_2 $1')
            ->editColumn('collection_amount', function ($shipments){
                return number_format($shipments->collection_amount);
            });
//        if($shipper = $request->get('search_shipper')){
//            $datatable->where('shipments.user_id','=', $shipper);
//        }
        if($origin = $request->get('search_origin')){
            $datatable->where('oc.id', '=', $origin);
        }
        if($destination = $request->get('search_destination')){
            $datatable->where('dc.id', '=', $destination);
        }
        if($card = $request->get('cards_filter')){
            switch ($card) {
                case 'total':
                    $today = Carbon::now()->endOfDay();
                    $thirtyDays = Carbon::now()->subDays(30)->startOfDay();
                    $datatable->whereBetween('shipments.created_at',[$thirtyDays,$today]);
                    break;
                case 'booked':
                    $datatable->where('shipments.shipper_status_id',1);
                    break;
                case 'received':
                    $datatable->whereIn('shipments.shipper_status_id',[2,3,4]);
                    break;
                case 'delivered':
                    $datatable->whereIn('shipments.shipper_status_id',[14,16, 30, 36,37,39,40,41,47]);
                    break;
                case 'returned':
                    $datatable->whereIn('shipments.shipper_status_id',[20,21,22,23,24,25,26,27,28,29,31,32,33,34,35,38,42,43,44,45,46,50]);
                    break;
                case 'in_process':
                    $datatable->whereIn('shipments.shipper_status_id',[5,6,7,8,9,10,11,12,13,15,18,19,49,52]);
                    break;
                case 'cancelled':
                    $datatable->where('shipments.shipper_status_id',17);
                    break;
            }
        }
        return $datatable->make(true);

    }


}

