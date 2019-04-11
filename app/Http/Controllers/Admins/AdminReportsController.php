<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Admin\DeliveryNoteStationDepositNote;
use App\Http\Models\Admin\PettyCashAccountHead;
use App\Http\Models\Admin\PettyCashAccountTitle;
use App\Http\Models\Admin\PettyCashStatement;
use App\Http\Models\Admin\PettyCashStatementDetail;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\Admin\ReturnNoteShipment;
use App\Http\Models\Admin\ReturnReattemptRatio;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\Admin\StationDepositNote;
use App\Http\Models\CargoConsignment;
use App\Http\Models\CargoConsignmentShipment;
use App\Http\Models\City;
use App\Http\Models\DonePaymentShipment;
use App\Http\Models\PendingPaymentShipment;
use App\Http\Models\PickupNote;
use App\Http\Models\PickupRequest;
use App\Http\Models\PickupRequestAssignedShipment;
use App\Http\Models\Rider;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\Shipper\User;
use App\Http\Models\ShippingMode;
use App\Http\Models\TransportModeVendor;
use App\Http\Models\Zone;
use App\Http\Models\Admin\GlobalSettings;
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
                    ->where('sj.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipments_journey as journey', function ($join) {
                $join->on('journey.shipment_id', '=', 'shipments.id')
                    ->where('journey.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->leftjoin('shipment_items as si', function ($join) {
                $join->on('si.shipment_id', '=', 'shipments.id')
                    ->where('si.type','=',0);
            })
            ->leftjoin('products as p','p.id','=','si.product_type_id')
            ->select(['p.product_name as product_type','si.description as description','shipments.id as shId','shipments.tracking_number','shipments.tracking_number as tracking_number_link','u.name as shipper','ss.name as history_status','bt.booking_type as service_type','sj.created_at as arrival','oc.name as origin','dc.name as destination','h.name as hub','shipments.amount','journey.created_at as last_status_date','shipments.consignee_name as name', 'shipments.booking_type_id', 'usi.poc'])
            ->whereNotIn('shipments.shipper_status_id',[1,14,16,17,25,31,36,38,39,40,41,43,47]);
        if (session('role_id') != 1) {
            $shipments = $shipments->whereIn('dc.hub_id', session('hubs'));
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
            ->leftjoin('return_note_shipments as rns','rns.return_note_id', '=', 'return_notes.id')
            ->leftjoin('shipments','shipments.id', '=', 'rns.shipment_id')
            ->join('admins as cr','cr.id','=','return_notes.admin_id')
            ->leftjoin('admins as up','up.id','=','return_notes.updated_by')
            ->select(['return_notes.id','return_notes.id as return_note_id','return_notes.id as return_note_link','up.name as updated_by','return_notes.shipments_count','return_notes.shipments_count as shipments_count_link','return_notes.updated_at as submission_date','riders.name as rider','cr.name as created_by','return_notes.created_at as created_at'])->groupBy('return_notes.id');
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
        $return_note_details = ReturnNote::find($return_note_id);
        $return_note_shipments = $return_note_details->return_note_shipments;
        $shipments = array();
        if($return_note_shipments->count() != 0){
            foreach ($return_note_shipments as $return_note_shipment){
                $shipment = Shipment::find($return_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Return Note Shipments', 'shipments' => $shipments];
        }else{
            return ['status' => 0, 'success' => 'No Return Note Shipments', 'shipments' => FALSE];
        }
    }
    public function return_note_print(Request $request){

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
            $filtered_shipments = Shipment::whereIn('id',$shipment_ids)->orderBy('id')->get();
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
    //Return Note Print end
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
            ->leftjoin('pickup_note_requests as pnr','pnr.pickup_note_id', '=', 'pickup_notes.id')
            ->leftjoin('pickup_requests as pr', 'pr.id', '=', 'pnr.pickup_request_id')
            ->select(['pr.shipper_id','pickup_notes.id as pn_id','pickup_notes.id as pickup_note_no','cities.name as city','pickup_notes.pickups', DB::raw('(select SUM(received) as received from pickup_requests where pickup_requests.id in (select pickup_request_id from pickup_note_requests where pickup_note_id = pickup_notes.id)) AS received'), 'riders.name as rider','pickup_notes.created_at as assigned_date','ab.name as assigned_by','pickup_notes.updated_at as completed_date','up.name as completed_by'])
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

        $pickup_note = PickupNote::find($pickup_note_id);
        $pickup_note_requests = $pickup_note->pickup_note_requests;

        if ($pickup_note_requests->count() != 0){
            $pickup_request_all_received_shipments = array();
            $bookings = array();

            foreach($pickup_note_requests as $pickup_note_request) {
                $pickup_request = PickupRequest::find($pickup_note_request->pickup_request_id);
                $shipper = $pickup_request->shipper->name;
                $pickup_request_all_received_shipments[$shipper] = $pickup_request->pickup_request_received_shipments;
                if ($pickup_request_all_received_shipments[$shipper]->count() != 0) {

                    foreach ($pickup_request_all_received_shipments[$shipper] as $all_shipments) {
                        $shipment = $all_shipments->shipment_id;
                        $shipment_details = Shipment::find($shipment);
                        $bookings[$shipper][] = $shipment_details->tracking_number;
                    }
                }

            }
            return ['status' => 0, 'success' => 'Booked Shipments', 'booked' => $bookings];

        }else{
            return ['status' => 0, 'success' => 'No Pickup requests found', 'booked' => FALSE];
        }


    }
    //pickup note print start
    public function pickup_note_assigned_print(Request $request) {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Pickup Note</title>

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

        foreach($request->ids as $id) {
            $pickup_note = PickupNote::find($id);

            if ($request->dispatch) {
                $pickup_note->status_id = 2;
                $pickup_note->updated_by = Auth::id();

                $pickup_note->save();
            }

            $rider = Rider::find($pickup_note->rider_id);
            $route = $rider->route;

            $html .= '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>
                            <td class="text-center align-middle color primary"><strong>Pickup Note</strong></td>
                            <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Rider Name</strong></td>
                            <td>' . $rider->name . '</td>
                            <td rowspan="7" class="text-center align-middle pl-1 pr-1">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Category</strong></td>
                            <td>' . $rider->rider_category->name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Route</strong></td>
                            <td> ' . $route->code . ' (' . $route->start . ' to ' . $route->end . ')</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>City</strong></td>
                            <td> ' . $pickup_note->rider->city->name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Pickups</strong></td>
                            <td>' . $pickup_note->pickups . '</td>
                          </tr>
                        </tbody>
                      </table>
        ';

            $html .= '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="color primary"><strong>S. No.</strong></td>
                            <td class="color primary"><strong>Company Name</strong></td>
                            <td class="color primary"><strong>Contact Person</strong></td>
                            <td class="color primary"><strong>Contact Number</strong></td>
                            <td class="color primary"><strong>Pickup Address</strong></td>
                            <td class="color primary"><strong>Bookings</strong></td>
                            <td class="color primary"><strong>Pickup Date</strong></td>
                          </tr>
        ';

            $serial_number = 1;

            $pickup_note_requests = $pickup_note->pickup_note_requests;

            foreach ($pickup_note_requests as $pickup_note_request) {
                $pickup_request = $pickup_note_request->pickup_request;

                $shipper = $pickup_request->shipper;
                $pickup_address = $pickup_request->pickup_address;

                $html .= '
                          <tr>
                            <td>' . $serial_number . '</td>
                            <td>' . $shipper->name . '</td>
                            <td>' . $pickup_address['poc'] . '</td>
                            <td>' . $pickup_address['phone'] . '</td>
                            <td>' . $pickup_address['pickup_address'] . '</td>
                            <td>' . $pickup_request['bookings'] . '</td>
                            <td>' . Carbon::parse($pickup_request['pickup_date'])->format('Y-m-d') . '</td>
                          </tr>
          ';

                $serial_number++;
            }

            $html .= '
                        </tbody>
                      </table>

                      <hr>
        ';
        }

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
    //pickup note print end
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
            ->select(['cargo_consignments.id as cargo_id','cargo_consignments.id as cargo_id_link','oc.name as origin','h.name as destination','cargo_consignments.shipments','cargo_consignments.shipments as shipments_link','sm.mode as shipping_mode','cargo_consignments.shipments_weight', DB::raw('(SELECT SUM(`s`.`chargeable_weight`) FROM `shipments` AS `s` INNER JOIN `cargo_consignment_shipments` AS `css` ON `s`.`id` = `css`.`shipment_id` WHERE `css`.`cargo_consignment_id` = `cargo_consignments`.`id`) AS `chargeable_weight`'), 'cargo_consignments.actual_weight', 'cargo_consignments.vendor_weight', 'cargo_consignments.created_at as transit_at','si.name as transit_by','ri.name as received_by','cargo_consignments.updated_at as received_at','cargo_consignments.received_shipments','cargo_consignments.type as cargo_type'])
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

        $cargo_consignments_shipments = CargoConsignmentShipment::where('cargo_consignment_id', $request->id)->get();

        foreach ($cargo_consignments_shipments as $cargo_consignments_shipment) {
            $shipment = $cargo_consignments_shipment->shipment;

            $tracking_number = array();

            $tracking_number['tracking_number'] = $shipment->tracking_number;
            $tracking_number['estimated_weight'] = $shipment->estimated_weight;
            $tracking_number['actual_weight'] = $shipment->actual_weight;
            $tracking_number['chargeable_weight'] = ($shipment->chargeable_weight) ? $shipment->chargeable_weight : '';

            $tracking_numbers[] = $tracking_number;
        }

        return $tracking_numbers;
    }
    public function cargo_print(Request $request) {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $cargo_consignment = CargoConsignment::find($request->id);

        $sender = $cargo_consignment->sender;
        $receiver = ($cargo_consignment->receiver_id) ? $cargo_consignment->receiver : NULL;

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Cargo Slip & Checklist</title>

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

                      .cargo_checklist {
                        page-break-before: always;
                      }
                    </style>
                  </head>
                  <body>
                    <div>
                      <div class="cargo_slip">
                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="text-center align-middle"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>
                              <td class="text-center align-middle color primary"><strong>Cargo Slip</strong></td>
                              <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                              </tr>
                            <tr>
                              <td class="color secondary"><strong>Destination Hub</strong></td>
                              <td>' . $cargo_consignment->destination_hub->name . '</td>
                              <td rowspan="8" class="text-center align-middle">
                                <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($cargo_consignment->id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                                <span><strong>' . str_pad($cargo_consignment->id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                              </td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Transit Date</strong></td>
                              <td>' . $cargo_consignment->created_at . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Shipping Mode</strong></td>
                              <td>' . $cargo_consignment->shipping_mode->mode . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Transport Mode</strong></td>
                              <td>' . $cargo_consignment->transport_mode->name . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Vendor</strong></td>
                              <td>' . $cargo_consignment->transport_mode_vendor->name . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Builty Number</strong></td>
                              <td>' . $cargo_consignment->builty_number . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Expected Arrival Date</strong></td>
                              <td>' . (($cargo_consignment->expected_arrival_date) ? Carbon::parse($cargo_consignment->expected_arrival_date)->format('d/m/Y') : '') . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>No. of Parcels</strong></td>
                              <td>' . $cargo_consignment->shipments . '</td>
                            </tr>
                          </tbody>
                        </table>

                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td colspan="2" class="color primary"><strong>Sender Information</strong></td>
                              <td colspan="2" class="color primary"><strong>Receiver Information</strong></td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Name</strong></td>
                              <td>' . $sender['name'] . '</td>
                              <td class="color secondary"><strong>Name</strong></td>
                              <td>' . (($receiver) ? $receiver['name'] : '') . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Role</strong></td>
                              <td>' . $sender->role->name  . ' - ' . $sender->role->department->name . '</td>
                              <td class="color secondary"><strong>Role</strong></td>
                              <td>' . (($receiver) ? $receiver->role->department->name : '') . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Phone No.</strong></td>
                              <td>' . $sender['phone_number'] . '</td>
                              <td class="color secondary"><strong>Phone No.</strong></td>
                              <td>' . (($receiver) ? $receiver['phone_number'] : '') . '</td>
                            </tr>
                          </tbody>
                        </table>

                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="color primary"><strong>Route Information</strong></td>
                            </tr>
                            <tr>
                              <td>' . $cargo_consignment->origin_hub->name . ' - ' . $cargo_consignment->junction_hub_1->name . ' - ' . (($cargo_consignment->junction_hub_2_id) ? ($cargo_consignment->junction_hub_2->name . ' - ') : '') . $cargo_consignment->destination_hub->name . '</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>

                      <div class="cargo_checklist">
                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="text-center align-middle"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>
                              <td class="text-center align-middle color primary"><strong>Cargo Checklist</strong></td>
                              <td class="text-center align-middle  color secondary">Printed at ' . Carbon::now() . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Origin Hub</strong></td>
                              <td>' . $cargo_consignment->origin_hub->name . '</td>
                              <td rowspan="5" class="text-center align-middle">
                                <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($cargo_consignment->id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                                <span><strong>' . str_pad($cargo_consignment->id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                              </td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Destination Hub</strong></td>
                              <td>' . $cargo_consignment->destination_hub->name . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Transit Date</strong></td>
                              <td>' . $cargo_consignment->created_at . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>Expected Arrival Date</strong></td>
                              <td>' . (($cargo_consignment->expected_arrival_date) ? Carbon::parse($cargo_consignment->expected_arrival_date)->format('d/m/Y') : '') . '</td>
                            </tr>
                            <tr>
                              <td class="color secondary"><strong>No. of Parcels</strong></td>
                              <td>' . $cargo_consignment->shipments . '</td>
                            </tr>
                          </tbody>
                        </table>

                        <table class="table table-sm table-bordered border">
                          <tbody>
                            <tr>
                              <td class="color primary"><strong>S. No.</strong></td>
                              <td class="color primary"><strong>Tracking No.</strong></td>
                              <td class="color primary"><strong>Consignee Name</strong></td>
                              <td class="color primary"><strong>Consignee Phone</strong></td>
                              <td class="color primary"><strong>Consignee City</strong></td>
                              <td class="color primary"><strong>Amount</strong></td>
      ';

        $serial_number = 1;

        foreach ($cargo_consignment->cargo_consignment_shipments as $cargo_consignment_shipment) {
            $shipment = $cargo_consignment_shipment->shipment;


            $html .= '
                            <tr>
                              <td>' . $serial_number . '</td>
                              <td>' . $shipment->tracking_number . '</td>
                              <td>' . $shipment->consignee_name . '</td>
                              <td>' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                              <td>' . $shipment->consignee_city->name . '</td>
                              <td>' . $shipment->amount . '</td>
                            </tr>
        ';

            $serial_number++;
        }

        $html .= '
                          </tbody>
                        </table>
                      </div>
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
    public function lead_time_index(Request $request){
//        $shippers = User::all(['id','name']);
        $cities = City::all(['id','name']);
        $shipper = User::all(['id','name']);
        $hubs = City::select(['id','name'])->where('hub',1)->get();
        $statuses = ShipmentStatus::all(['id','name']);
        return view('admin.reports.lead_time_report')->with(['cities'=>$cities,'statuses'=>$statuses,'hubs'=>$hubs,'shipper'=>$shipper]);
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
                    ->where('sj.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipments_journey as radd', function ($join) {
                $join->on('radd.shipment_id', '=', 'shipments.id')
                    ->where('radd.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 4)'));
            })
            ->leftJoin('shipments_journey as fstatus', function ($join) {
                $join->on('fstatus.shipment_id', '=', 'shipments.id')
                    ->where('fstatus.id','>',
                        DB::raw('(select min(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 5)'));
            })
            ->leftJoin('shipments_journey as lstatus', function ($join) {
                $join->on('lstatus.shipment_id', '=', 'shipments.id')
                    ->where('lstatus.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.verification = 0)'));
            })
            ->leftJoin('shipments_journey as dd', function ($join) {
                $join->on('dd.shipment_id', '=', 'shipments.id')
                    ->where('dd.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,16,30,36) )'));
            })
            ->leftJoin('shipments_journey as rc', function ($join) {
                $join->on('rc.shipment_id', '=', 'shipments.id')
                    ->where('rc.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(20,42))'));
            })
            ->leftJoin('shipments_journey as rrad', function ($join) {
                $join->on('rrad.shipment_id', '=', 'shipments.id')
                    ->where('rrad.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 22)'));
            })
            ->leftJoin('shipments_journey as rds', function ($join) {
                $join->on('rds.shipment_id', '=', 'shipments.id')
                    ->where('rds.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(24,25,29,31,35,38))'));
            })
            ->leftJoin('shipments_journey as pd', function ($join) {
                $join->on('pd.shipment_id', '=', 'shipments.id')
                    ->where('pd.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(39,40,41,43))'));
            })
            ->leftJoin('shipments_journey as ret_or_del', function ($join) {
                $join->on('ret_or_del.shipment_id', '=', 'shipments.id')
                    ->where('ret_or_del.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,20,30,36,37,42))'));
            })
            ->leftJoin('shipments_journey as lj', function ($join) {
                $join->on('lj.shipment_id', '=', 'shipments.id')
                    ->where('lj.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id)'));
            })
            ->leftJoin('delivery_note_shipments as dns', function ($join) {
                $join->on('dns.shipment_id', '=', 'shipments.id')
                    ->where('dns.delivery_note_id','=',
                        DB::raw('(select min(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = shipments.id)'));
            })
            ->leftJoin('shipments_journey as fatstatus', function ($join) {
                $join->on('fatstatus.shipment_id', '=', 'shipments.id')
                    ->where('fatstatus.id','=',
                        DB::raw('(select min(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 5)'));
            })
            ->leftJoin('shipment_status as fs','fs.id','=','fstatus.shipper_status_id')
            ->leftJoin('shipment_status as ls','ls.id','=','lstatus.shipper_status_id')
            ->leftJoin('shipment_status as rdss','rdss.id','=','rds.shipper_status_id')
            ->leftjoin('delivery_note_shipments as dnss', function ($join) {
                $join->on('dnss.shipment_id', '=', 'shipments.id')
                    ->where('dnss.delivery_note_id','=',
                        DB::raw('(select max(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = shipments.id)'));
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
                        DB::raw('(select max(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = shipments.id)'));
            })
            ->leftjoin('delivery_note_shipments as fdnsv', function($join){
                $join->on('fdnsv.shipment_id','=','shipments.id')
                    ->where('fdnsv.delivery_note_id','=',
                        DB::raw('(select min(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = shipments.id)'));
            })
//            ->leftjoin('shipments_journey as lss','lss.shipper_status_id','=',[7, 8, 9, 10, 11, 12, 15, 18])
            ->leftjoin('delivery_note_shipments as ldnsv', function($join){
                $join->on('ldnsv.shipment_id','=','shipments.id')
                    ->where('ldnsv.delivery_note_id','=',
                        DB::raw('(select max(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = shipments.id)'));
            })
            ->leftjoin('shipments_journey as fsjv',function($join) {
                $join->on('fsjv.reference_1_id', '=', 'fdnsv.delivery_note_id')
                    ->where('fsjv.id','=', DB::raw('(select min(id) from shipments_journey where shipments_journey.reference_1_id = fdnsv.delivery_note_id and shipments_journey.shipper_status_id > 5 and shipments_journey.verification = 1)'));
            })
            ->leftjoin('shipment_status as fssv','fssv.id','=','fsjv.shipper_status_id')
            ->leftjoin('shipments_journey as lsjv',function($join) {
                $join->on('lsjv.reference_1_id', '=', 'ldnsv.delivery_note_id')
                    ->where('lsjv.id','=', DB::raw('(select max(id) from shipments_journey  where shipments_journey.reference_1_id = ldnsv.delivery_note_id and shipments_journey.verification = 1)'));
            })
			->leftjoin('cargo_consignment_shipments as cccc',function($join){
                $join->on('cccc.shipment_id', '=', 'shipments.id')
                    ->where('cccc.id', '=', DB::raw('(select min(id) from cargo_consignment_shipments where cargo_consignment_shipments.shipment_id = shipments.id)'));
            })
            ->leftjoin('cargo_consignment_shipments as ccrc',function($join){
                $join->on('ccrc.shipment_id', '=', 'shipments.id')
                    ->where('ccrc.id', '=', DB::raw('(select max(id) from cargo_consignment_shipments where cargo_consignment_shipments.shipment_id = shipments.id)'));
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
                    $name = TransportModeVendor::where('transport_mode_vendors.id', $shipments->vendor)->first();
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
            ->join('delivery_notes as delivery_note', 'delivery_note_shipments.delivery_note_id', '=', 'delivery_note.id')
            ->join('riders as rider', 'delivery_note.rider_id', '=', 'rider.id')
            ->join('cities as hc', 'dc.hub_id', '=', 'hc.id')
            ->join('users as u', 's.user_id', '=', 'u.id')
            ->join('booking_types as bt', 's.booking_type_id', '=', 'bt.id')
            ->join('user_shipping_infos AS usi', 's.pickup_address_id', '=', 'usi.id')
            ->leftjoin('shipment_payment_status as sps', 's.payment_status_id', '=' , 'sps.id')
            ->leftjoin('shipments_journey as sj', function($join) {
                $join->on('sj.shipment_id', '=', 's.id')
                    ->where('sj.id', '=', DB::raw('(SELECT MAX(id) FROM shipments_journey WHERE shipment_id = s.id)'));
            })
            ->leftjoin('shipments_journey as sod', function($join) {
                $join->on('sod.shipment_id', '=', 's.id')
                    ->where('sod.id', '=', DB::raw('(SELECT MAX(id) FROM shipments_journey WHERE shipment_id = s.id and shipments_journey.verification = 0 and shipments_journey.reference_1_id = delivery_note.id)'));
            })
            ->leftjoin('shipments_journey as svd', function($join) {
                $join->on('svd.shipment_id', '=', 's.id')
                    ->where('svd.id', '=', DB::raw('(SELECT MAX(id) FROM shipments_journey WHERE shipment_id = s.id and shipments_journey.verification = 1 and shipments_journey.reference_1_id = delivery_note.id and shipments_journey.shipper_status_id != 5)'));
            })
            ->leftjoin('shipments_journey as sjd', function($join) {
                $join->on('sjd.shipment_id', '=', 's.id')
                    ->where('sjd.id', '=', DB::raw('(SELECT MAX(id) FROM shipments_journey WHERE shipment_id = s.id AND shipper_status_id IN (14, 16, 30, 36))'));
            })
            ->join('shipment_status as ss', 'sj.shipper_status_id', '=', 'ss.id')
            ->leftjoin('delivery_note_station_deposit_notes as dnsdn', 'delivery_note_shipments.delivery_note_id', '=', 'dnsdn.delivery_note_id')
            ->select('s.id', 's.tracking_number', 's.consignee_name as consignee', 's.consignee_address as address', 'dc.name as destination', 'hc.name as hub', 'u.name as shipper', 'bt.booking_type as service_type', 's.amount', 'ss.name as current_status', 'sod.created_at as operation_status_date','svd.created_at as verification_status_date', 'sj.remarks', 'delivery_note_shipments.delivery_note_id as dncc', 'delivery_note_shipments.delivery_note_id as dncc_link', 'dnsdn.station_deposit_note_id as sdn', 'dnsdn.station_deposit_note_id as sdn_link', 'sjd.created_at as delivered_at','delivery_note_shipments.status as recovery_status','sps.name as payment_status','rider.name as rider_name', 's.booking_type_id', 'usi.poc')
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

    //print dncc
    public function dncc_print(Request $request)
    {

        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Delivery Note Cash Collection</title>

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
                      
                      .w-150 {
                        width: 150px;
                      }
                      
                      .w-200 {
                        width: 200px;
                      }

                      .line {
                        border-bottom: 1px solid #09262e !important;
                      }

                      .manual_form {
                        page-break-inside: avoid;
                      }
                    </style>
                  </head>
                  <body>
                    <div>
      ';
        $delivery_note = DeliveryNote::where('id', $request->id);
        if ($delivery_note->exists()) {
            $delivery_note_data = $delivery_note->first();
            $delivery_note_data->last_updated_at = Carbon::now();
            $delivery_note_data->save();
            $total_shipments = 0;
            $total_cod_amount = 0;
            $dncc_status = array(14, 16, 30, 36, 37);
            $shipment_ids = DeliveryNoteShipment::where('delivery_note_id', $request->id)->where('status','>',1)->where('status','!=',8)->select('shipment_id')->get();
            $filtered_shipments = Shipment::whereIn('id', $shipment_ids)->whereIn('shipper_status_id', $dncc_status)->orderBy('id')->get();
            //echo "<pre>";print_r($filtered_shipments);echo "</pre>";die();
            $shipment_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="color primary"><strong>S. No.</strong></td>
                            <td class="color primary"><strong>Tracking No.</strong></td>
                            <td class="color primary"><strong>Consignee Name & Phone No(s).</strong></td>
                            <td class="color primary"><strong>Consignee Address</strong></td>
                            <td class="color primary"><strong>Service Type</strong></td>
                            <td class="color primary"><strong>Client Name & Phone</strong></td>
                            <td class="color primary"><strong>Weight</strong></td>
                            <td class="color primary"><strong>Collection Amount</strong></td>
                          </tr>
        ';


            foreach ($filtered_shipments as $shipment) {
                $total_shipments++;
//                    $shipment = Shipment::find($parcel->shipment_id);

                $shipment_details_row_start = '
                          <tr>
                            <td>' . $total_shipments . '</td>
                            <td>' . $shipment->tracking_number . '</td>
                            <td>' . $shipment->consignee_name . ' | ' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                            <td>' . $shipment->consignee_address . '</td>
                            <td>' . $shipment->booking_type->booking_type . '</td>
                            <td>' . $shipment->user->name . ' | ' . $shipment->user->phone . (($shipment->phone2) ? (' / ' . $shipment->phone2) : '') . '</td>
                            <td>' . (($shipment->booking_type_id == 2) ? $shipment->replacement_weight : $shipment->actual_weight) . '</td>
                            <td>Rs ' . number_format($shipment->received_amount) . '</td>
                            
                          </tr>
            ';
                $total_cod_amount += $shipment->received_amount;
                $shipment_details .= $shipment_details_row_start;
            }
            $shipment_details .= '
                        </tbody>
                      </table>
        ';
            $delivery_note_details = DeliveryNote::where('id', $request->id)->first();
            $rider = Rider::where('id', $delivery_note_details->rider_id)->first();
            $city_name = $delivery_note_details->hub->name;
            $rider_name = $rider->name;
            $category = $rider->rider_category->name;
            $route_name = $delivery_note_details->route->code . ' (' . $delivery_note_details->route->start . ' to ' . $delivery_note_details->route->end . ')';
            $main_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>';
            if ($request->has('temporary') && ($request->temporary != null)) {
                $main_details .= '<td class="text-center align-middle color primary"><strong>Temporary Cash Collection</strong></td>';
            } else {
                $main_details .= '<td class="text-center align-middle color primary"><strong>Delivery Note Cash Collection</strong></td>';
            }


            $main_details .= '<td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Delivery Note No.</strong></td>
                            <td>' . str_pad($delivery_note_details->id, 6, '0', STR_PAD_LEFT) . '</td>
                          </tr>
                          <tr>
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
                            <td>' . $city_name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Shipments</strong></td>
                            <td>' . $delivery_note_details->shipments_count . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Delivered Shipments</strong></td>
                            <td>' . $total_shipments . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>DNCC Amount</strong></td>
                            <td>Rs ' . number_format($total_cod_amount) . '</td>
                          </tr>
                        </tbody>
                      </table>
        ';
            $html .= $main_details;
            $html .= $shipment_details;
            $html .= '
                      <div class="mt-2 manual_form">
                      <div class="row  mt-1">
                         <div class="col">
                            <div class="text-right">
                                <span class="d-inline-block w-150 text-left"><strong>DNCC Amount</strong></span>
                                <strong>Rs. '.number_format($total_cod_amount).'</strong>
                            </div>
                          </div>
                        </div>
                        <hr>
                        
                        <div class="row justify-content-center align-items-end mt-5">
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Rider Name</strong>
                            </div>
                          </div>
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Rider Signature</strong>
                            </div>
                          </div>
                        </div>
                        <div class="row justify-content-between align-items-end mt-5">
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Operation Staff Name</strong>
                            </div>
                          </div>
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Operation Staff Signature</strong>
                            </div>
                          </div>
                        </div>
                        <div class="row justify-content-between align-items-end mt-5">
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Cashier Name</strong>
                            </div>
                          </div>
                          <div class="col justify-content-center ">
                            <div class="text-center">
                              <span class="d-block w-200 mx-auto line"></span>
                              <strong class="d-inline-block w-200">Cashier Signature</strong>
                            </div>
                          </div>
                        </div>
                      </div>
        ';
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
    public function sdn_print(Request $request)
    {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Station Deposit Note</title>

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

        $sdn = StationDepositNote::where('id', $request->id);
        if ($sdn->exists()) {
            $total_dncc = 0;

//            $shipments = DeliveryNoteShipment::where('delivery_note_id',$request->id)->select('shipment_id')->get();
            $dncc_ids = DeliveryNoteStationDepositNote::where('station_deposit_note_id', $request->id)->select('delivery_note_id')->get();
            $shipment_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="color primary"><strong>S. No.</strong></td>
                            <td class="color primary"><strong>DN No.</strong></td>
                            <td class="color primary"><strong>Rider Name</strong></td>
                            <td class="color primary"><strong>Route</strong></td>
                            <td class="color primary"><strong>Total No. Of Shipments</strong></td>
                            <td class="color primary"><strong>No. Of Delivered Shipments</strong></td>
                            <td class="color primary"><strong>Collection Amount</strong></td>
                          </tr>
        ';


            foreach ($dncc_ids as $dncc) {
                $total_dncc++;
                $dncc_note = DeliveryNote::find($dncc->delivery_note_id);

                $shipment_details_row_start = '
                          <tr>
                            <td>' . $total_dncc . '</td>
                            <td>' . str_pad($dncc_note->id, 6, '0', STR_PAD_LEFT) . '</td>
                            <td>' . $dncc_note->rider->name . '</td>
                            <td>' . $dncc_note->route->code . '( ' . $dncc_note->route->start . ' to ' . $dncc_note->route->end . ' )' . '</td>
                            <td>' . $dncc_note->shipments_count . '</td>
                            <td>' . $dncc_note->delivered_shipments . '</td>
                            <td>Rs ' . number_format($dncc_note->received_cod_amount) . '</td>
                            
                          </tr>
            ';

                $shipment_details .= $shipment_details_row_start;
            }
            $shipment_details .= '
                        </tbody>
                      </table>
        ';
            $station_note_details = StationDepositNote::where('id', $request->id)->first();
            $city_name = $station_note_details->hub->name;
            $main_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>
                            <td class="text-center align-middle color primary"><strong>Station Deposit Note</strong></td>
                            <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Hub Name</strong></td>
                            <td>' . $city_name . '</td>
                            <td rowspan="7" class="text-center align-middle p-1">
                              <img src="data:image/png;base64,' . base64_encode($generator->getBarcode(str_pad($request->id, 6, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128, 2, 60)) . '" class="d-block mx-auto">
                              <span><strong>' . str_pad($request->id, 6, '0', STR_PAD_LEFT) . '</strong></span>
                            </td>
                          </tr>                         
                          <tr>
                            <td class="color secondary"><strong>Total DNCC Amount</strong></td>
                            <td>Rs ' . number_format($station_note_details->sdn_amount) . '</td>
                          </tr>
                         <!-- <tr>
                            <td class="color secondary"><strong>Total Expenses</strong></td>
                            <td>' . number_format($station_note_details->sdn_expense) . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Net Amount</strong></td>
                            <td>' . number_format($station_note_details->sdn_net_amount) . '</td>
                          </tr>-->
                          
                          <tr>
                            <td class="color secondary"><strong>Bank Name</strong></td>
                            <td>' . $station_note_details->bank->name . '</td>
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
    //outstanding shipments end
    public function daily_pickup_sales_index(Request $request){

        if (session('role_id') == 1){
            $cities = City::select('id','name')->where('pickup',1)->get();
        }else{
            $cities = City::select('id','name')->where('pickup',1)->whereIn('hub_id',session('hubs'))->get();
        }
        $sales = '';
        if(session('department_id') == 7){
            $sales = Admin::whereHas('role', function($query) {
                $query->where('department_id', '=', 7);
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

                    $city = City::find($search_city);
                    $search_city_hub = $city->id;
                    $city['id'] = $city->id;
                    $city['name'] = $city->name;
                    $hubs[] = $city;
                } else {
                    $search_city_hub = '';
                    $hubs = City::where('pickup', 1)->select('id', 'name')->get();
                }
            } else {
                if ($search_city != null) {

                    $city = City::find($search_city);
                    $search_city_hub = $city->id;
                    $city['id'] = $city->id;
                    $city['name'] = $city->name;
                    $hubs[] = $city;
                } else {
                    $search_city_hub = '';
                    $hubs = City::whereIn('hub_id', session('hubs'))->where('pickup', 1)->select('id', 'name')->get();
                }
            }
        }else{
            $search_city_hub = '';
            $hubs = City::where('pickup', 1)->select('id', 'name')->get();
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
        $serial_number_hubs = 1;
        $booked = 0;
        $received = 0;
        $revenue_wo_gst = 0;
        $cod_collection = 0;
        $actual_weight = 0;
        $chargeable_weight = 0;
        foreach ($hubs as $hub) {
            if($sales_tagging == TRUE){

                if (session('department_id') != 7){
                    $booked = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                        $query->where('id', '=', $hub->id);
                    })->whereBetween('created_at',[$date_from,$date_to])->where('shipments.packaging_material_request', 0)->count();
                    $received = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                        $query->where('id', '=', $hub->id);
                    })->whereHas('shipment_journey', function($query) use ($date_from, $date_to) {
                        $query->whereBetween('created_at',[$date_from,$date_to])
                            ->where('shipper_status_id', 2);
                    })->where('shipments.packaging_material_request', 0)->count();
                    $cod_collection = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                        $query->where('id', '=', $hub->id);
                    })->whereHas('shipment_journey', function($query) use ($date_from, $date_to) {
                        $query->whereBetween('created_at',[$date_from,$date_to])
                            ->where('shipper_status_id', 2);
                    })->where('shipments.packaging_material_request', 0)->sum('amount');
                    $actual_weight = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                        $query->where('id', '=', $hub->id);
                    })->whereHas('shipment_journey', function($query) use ($date_from, $date_to) {
                        $query->whereBetween('created_at',[$date_from,$date_to])
                            ->where('shipper_status_id', 2);
                    })->where('shipments.packaging_material_request', 0)->sum('actual_weight');
                    $chargeable_weight = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                        $query->where('id', '=', $hub->id);
                    })->whereHas('shipment_journey', function($query) use ($date_from, $date_to) {
                        $query->whereBetween('created_at',[$date_from,$date_to])
                            ->where('shipper_status_id', 2);
                    })->where('shipments.packaging_material_request', 0)->sum('chargeable_weight');
                    $revenue_wo_gst = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                        $query->where('id', '=', $hub->id);
                    })->whereHas('shipment_journey', function($query) use ($date_from, $date_to) {
                        $query->whereBetween('created_at',[$date_from,$date_to])
                            ->where('shipper_status_id', 2);
                    })->where('shipments.packaging_material_request', 0)->sum(DB::raw('IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)'));

                }else{
                    if(session('role_id') != 4){
                        $booked = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                            $query->where('id', '=', $hub->id);
                        })->where('shipments.packaging_material_request', 0)->whereBetween('created_at',[$date_from,$date_to])->whereIn('shipments.user_id', session('tagged_shippers'))->count();
                        $received = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                            $query->where('id', '=', $hub->id);
                        })->whereHas('shipment_journey', function($query) use ($date_from, $date_to) {
                            $query->whereBetween('created_at',[$date_from,$date_to])
                                ->where('shipper_status_id', 2);
                        })->where('shipments.packaging_material_request', 0)->whereIn('shipments.user_id', session('tagged_shippers'))->count();
                        $cod_collection = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                            $query->where('id', '=', $hub->id);
                        })->whereHas('shipment_journey', function($query) use ($date_from, $date_to) {
                            $query->whereBetween('created_at',[$date_from,$date_to])
                                ->where('shipper_status_id', 2);
                        })->where('shipments.packaging_material_request', 0)->whereIn('shipments.user_id', session('tagged_shippers'))->sum('amount');
                        $actual_weight = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                            $query->where('id', '=', $hub->id);
                        })->whereHas('shipment_journey', function($query) use ($date_from, $date_to) {
                            $query->whereBetween('created_at',[$date_from,$date_to])
                                ->where('shipper_status_id', 2);
                        })->where('shipments.packaging_material_request', 0)->whereIn('shipments.user_id', session('tagged_shippers'))->sum('actual_weight');
                        $chargeable_weight = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                            $query->where('id', '=', $hub->id);
                        })->whereHas('shipment_journey', function($query) use ($date_from, $date_to) {
                            $query->whereBetween('created_at',[$date_from,$date_to])
                                ->where('shipper_status_id', 2);
                        })->where('shipments.packaging_material_request', 0)->whereIn('shipments.user_id', session('tagged_shippers'))->sum('chargeable_weight');
                        $revenue_wo_gst = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                            $query->where('id', '=', $hub->id);
                        })->whereHas('shipment_journey', function($query) use ($date_from, $date_to) {
                            $query->whereBetween('created_at',[$date_from,$date_to])
                                ->where('shipper_status_id', 2);
                        })->where('shipments.packaging_material_request', 0)->whereIn('shipments.user_id', session('tagged_shippers'))->sum(DB::raw('IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)'));
                    }else{
                        if($sales_person != null){
                            $tagged_shippers = SalePersonTag::where('admin_id', $sales_person)->select('user_id')->get();

                            $booked = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                                $query->where('id', '=', $hub->id);
                            })->where('shipments.packaging_material_request', 0)->whereBetween('created_at',[$date_from,$date_to])->whereIn('user_id', $tagged_shippers)->count();

                            $received = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                                $query->where('id', '=', $hub->id);
                            })->whereHas('shipment_journey', function($query) use ($date_from, $date_to) {
                                $query->whereBetween('created_at',[$date_from,$date_to])
                                    ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', 0)->whereIn('user_id', $tagged_shippers)->count();
                            $cod_collection = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                                $query->where('id', '=', $hub->id);
                            })->whereHas('shipment_journey', function($query) use ($date_from, $date_to) {
                                $query->whereBetween('created_at',[$date_from,$date_to])
                                    ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', 0)->whereIn('user_id', $tagged_shippers)->sum('amount');
                            $actual_weight = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                                $query->where('id', '=', $hub->id);
                            })->whereHas('shipment_journey', function($query) use ($date_from, $date_to) {
                                $query->whereBetween('created_at',[$date_from,$date_to])
                                    ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', 0)->whereIn('user_id', $tagged_shippers)->sum('actual_weight');
                            $chargeable_weight = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                                $query->where('id', '=', $hub->id);
                            })->whereHas('shipment_journey', function($query) use ($date_from, $date_to) {
                                $query->whereBetween('created_at',[$date_from,$date_to])
                                    ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', 0)->whereIn('user_id', $tagged_shippers)->sum('chargeable_weight');
                            $revenue_wo_gst = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                                $query->where('id', '=', $hub->id);
                            })->whereHas('shipment_journey', function($query) use ($date_from, $date_to) {
                                $query->whereBetween('created_at',[$date_from,$date_to])
                                    ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', 0)->whereIn('user_id', $tagged_shippers)->sum(DB::raw('IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)'));

                        }else{

                            $booked = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                                $query->where('id', '=', $hub->id);
                            })->where('shipments.packaging_material_request', 0)->whereBetween('created_at',[$date_from,$date_to])->count();
                            $received = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                                $query->where('id', '=', $hub->id);
                            })->whereHas('shipment_journey', function($query) use ($date_from, $date_to) {
                                $query->whereBetween('created_at',[$date_from,$date_to])
                                    ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', 0)->count();
                            $cod_collection = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                                $query->where('id', '=', $hub->id);
                            })->whereHas('shipment_journey', function($query) use ($date_from, $date_to) {
                                $query->whereBetween('created_at',[$date_from,$date_to])
                                    ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', 0)->sum('amount');
                            $actual_weight = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                                $query->where('id', '=', $hub->id);
                            })->whereHas('shipment_journey', function($query) use ($date_from, $date_to) {
                                $query->whereBetween('created_at',[$date_from,$date_to])
                                    ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', 0)->sum('actual_weight');
                            $chargeable_weight = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                                $query->where('id', '=', $hub->id);
                            })->whereHas('shipment_journey', function($query) use ($date_from, $date_to) {
                                $query->whereBetween('created_at',[$date_from,$date_to])
                                    ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', 0)->sum('chargeable_weight');
                            $revenue_wo_gst = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                                $query->where('id', '=', $hub->id);
                            })->whereHas('shipment_journey', function($query) use ($date_from, $date_to) {
                                $query->whereBetween('created_at',[$date_from,$date_to])
                                    ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', 0)->sum(DB::raw('IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)'));

                        }
                    }
                }

            }else{
                $booked = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                    $query->where('id', '=', $hub->id);
                })->where('shipments.packaging_material_request', 0)->whereBetween('created_at',[$date_from,$date_to])->count();
                $received = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                    $query->where('id', '=', $hub->id);
                })->whereHas('shipment_journey', function($query) use ($date_from, $date_to) {
                    $query->whereBetween('created_at',[$date_from,$date_to])
                        ->where('shipper_status_id', 2);
                })->where('shipments.packaging_material_request', 0)->count();
                $cod_collection = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                    $query->where('id', '=', $hub->id);
                })->whereHas('shipment_journey', function($query) use ($date_from, $date_to) {
                    $query->whereBetween('created_at',[$date_from,$date_to])
                        ->where('shipper_status_id', 2);
                })->where('shipments.packaging_material_request', 0)->sum('amount');
                $actual_weight = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                    $query->where('id', '=', $hub->id);
                })->whereHas('shipment_journey', function($query) use ($date_from, $date_to) {
                    $query->whereBetween('created_at',[$date_from,$date_to])
                        ->where('shipper_status_id', 2);
                })->where('shipments.packaging_material_request', 0)->sum('actual_weight');
                $chargeable_weight = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                    $query->where('id', '=', $hub->id);
                })->whereHas('shipment_journey', function($query) use ($date_from, $date_to) {
                    $query->whereBetween('created_at',[$date_from,$date_to])
                        ->where('shipper_status_id', 2);
                })->where('shipments.packaging_material_request', 0)->sum('chargeable_weight');
                $revenue_wo_gst = Shipment::whereHas('pickup_address.city', function($query) use ($hub) {
                    $query->where('id', '=', $hub->id);
                })->whereHas('shipment_journey', function($query) use ($date_from, $date_to) {
                    $query->whereBetween('created_at',[$date_from,$date_to])
                        ->where('shipper_status_id', 2);
                })->where('shipments.packaging_material_request', 0)->sum(DB::raw('IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)'));
            }
            if($booked > 0 || $received > 0){

                $row = array();
                $avg_revenue = ($received != 0)? $revenue_wo_gst/$received:0;
                $avg_cash_collection = ($received != 0)? $cod_collection/$received:0;
                $rev_on_cash_collection = (($avg_cash_collection != 0)? $avg_revenue/$avg_cash_collection:0)*100;
                //changes add columns
                $avg_actual_weight = ($actual_weight != 0)? $actual_weight/$received:0;
                $avg_rev_actual_weight = ($actual_weight != 0 && $revenue_wo_gst != 0)? $revenue_wo_gst/$actual_weight:0;
                $avg_chargeable_weight = ($chargeable_weight != 0)? $chargeable_weight/$received:0;
                $avg_rev_chargeable_weight = ($chargeable_weight != 0 && $revenue_wo_gst != 0)? $revenue_wo_gst/$chargeable_weight:0;

                //end changes
                $row['serials'] = $serial_number_hubs;
                $row['hub'] = $hub->name;
                $row['booked'] = number_format($booked);
                $row['received'] = number_format($received);
                $row['revenue_wo_gst'] = number_format($revenue_wo_gst);
                $avg_rev = round($avg_revenue);
                $row['average_revenue'] = number_format($avg_rev);
                $string_actual_weight = (string) $actual_weight;
                $row['actual_weight'] = number_format((float)$string_actual_weight,2,'.','');
                $row['avg_actual_weight'] = round($avg_actual_weight);
                $row['avg_rev_actual_weight'] = round($avg_rev_actual_weight);

                $row['chargeable_weight'] = number_format((float)$chargeable_weight,2,'.','');
                $row['avg_chargeable_weight'] = round($avg_chargeable_weight);
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
                $total_avg_cash_collection += $avg_cc;
//                $total_rev_on_cash_collection += $rev_occ;
                $serial_number_hubs++;
            }

        }
        $total_avg_revenue = ($total_revenue_wo_gst != 0) ? $total_revenue_wo_gst / $total_received:0;
        $total_avg_actual_weight = ($total_actual_weight != 0) ? $total_actual_weight / $total_received:0;
        $total_avg_rev_actual_weight = ($total_revenue_wo_gst != 0) ? $total_revenue_wo_gst / $total_actual_weight:0;
        $total_avg_chargeable_weight = ($total_chargeable_weight != 0) ? $total_chargeable_weight / $total_received:0;
        $total_avg_rev_chargeable_weight = ($total_revenue_wo_gst != 0) ? $total_revenue_wo_gst / $total_chargeable_weight:0;
        $total_rev_on_cash_collection = ($total_avg_revenue != 0) ? $total_avg_revenue / $total_avg_cash_collection:0;
        array_multisort($sort_support_array, SORT_DESC, $sorted_details_array);
        foreach ($sorted_details_array as $item) {
            $details[] = $item;
        }
        $details[] = ['Grand Total','Origin '.$only_date, number_format($total_booked), number_format($total_received), number_format($total_revenue_wo_gst),number_format($total_avg_revenue),number_format((float)$total_actual_weight,2,'.',''),round($total_avg_actual_weight),round($total_avg_rev_actual_weight),number_format((float)$total_chargeable_weight,2,'.',''),round($total_avg_chargeable_weight),round($total_avg_rev_chargeable_weight), number_format($total_cod_collection),number_format($total_avg_cash_collection),number_format($total_rev_on_cash_collection).'%'];

        $details_shipper['header'] = ['S. No.','DSR '.$only_date, 'No. of Parcels Booked','No of Parcels Received','Revenue without GST','Avg/Parcel Revenue','Actual Weight','Avg. Actual Weight/Parcel','Avg. Revenue On Actual Weight','Chargeable Weight','Avg. Chargeable Weight/Parcel','Avg. Revenue On Chargeable Weight','Collection Amount','Avg. Amount Collection','% Rev. on Amount Collection'];

//        $details_shipper['header'] = ['S. No.','DSR '.$only_date, 'No. of Parcels Booked','No of Parcels Received','Revenue without GST','Collection Amount','Actual Weight','Chargeable Weight','Avg/Parcel Revenue','Avg. Amount Collection','% Rev. on Amount Collection'];
        $serial_number_shippers = 1;

        if($sales_tagging == TRUE){
            if($search_city != null){
                $pickup_request_shippers = Shipment::whereHas('pickup_address.city', function($query) use ($search_city_hub) {
                    $query->where('id', '=', $search_city_hub);
                })->whereHas('shipment_journey', function($query) use ($date_from, $date_to) {
                    $query->whereBetween('created_at',[$date_from,$date_to])
                        ->where('shipper_status_id', 2);
                });
//                $pickup_request_shippers = PickupRequest::whereHas('pickup_address.city', function($query) use($search_city_hub) {
//                    $query->where('id', '=', $search_city_hub);
//                })->whereBetween('created_at',[$date_from,$date_to])->select('shipper_id');
                if($pickup_request_shippers->exists()){
                    $pickup_request_shippers_ids = $pickup_request_shippers->pluck('user_id')->toArray();

                    if(session('department_id') != 7){
                        $shippers = User::select('id','name')->whereIn('id',$pickup_request_shippers_ids)->where('status',3)->get();
                    }else{
                        if(session('role_id') != 4){
                            $shippers = User::select('id','name')->whereIn('id',$pickup_request_shippers_ids)->where('status',3)->whereIn('id', session('tagged_shippers'))->get();
                        }else{
                            $shippers = User::select('id','name')->whereIn('id',$pickup_request_shippers_ids)->where('status',3)->get();
                        }
                    }

                }
            }else{
                $pickup_request_shippers = Shipment::whereHas('shipment_journey', function($query) use ($date_from, $date_to) {
                    $query->whereBetween('created_at',[$date_from,$date_to])
                        ->where('shipper_status_id', 2);
                });
                if($pickup_request_shippers->exists()) {
                    $pickup_request_shippers_ids = $pickup_request_shippers->pluck('user_id')->toArray();

//                    $pickup_request_shippers_ids = Shipment::whereIn('id', $pickup_request_shippers_ids)->groupBy('user_id');

                    if (session('department_id') != 7) {
//                        if ($pickup_request_shippers_ids->exists()) {
//                            $pickup_request_shippers_ids = $pickup_request_shippers_ids->get();

                        $shippers = User::select('id', 'name')->whereIn('id', $pickup_request_shippers_ids)->where('status', 3)->get();
//                        }
                    } else {
                        if (session('role_id') != 4) {
//                            if ($pickup_request_shippers_ids->exists()) {
//                                $pickup_request_shippers_ids = $pickup_request_shippers_ids->get();
                            $shippers = User::select('id', 'name')->whereIn('id', $pickup_request_shippers_ids)->where('status', 3)->whereIn('id', session('tagged_shippers'))->get();
//                            }


                        } else {
                            if ($sales_person != null) {

//                                if ($pickup_request_shippers_ids->exists()) {
//                                    $pickup_request_shippers_ids = $pickup_request_shippers_ids->get();
                                $shippers = User::whereHas('sales_person', function ($query) use ($sales_person) {
                                    $query->where('admin_id', $sales_person);
                                })->whereIn('id', $pickup_request_shippers_ids)->where('status', 3)->select('id', 'name')->get();
//                                }

                            } else {
//                                if ($pickup_request_shippers_ids->exists()) {
//                                    $pickup_request_shippers_ids = $pickup_request_shippers_ids->get();
                                $shippers = User::select('id', 'name')->whereIn('id', $pickup_request_shippers_ids)->where('status', 3)->get();
//                                }
                            }

                        }
                    }
                }
            }
        }
        else{
            $pickup_request_shippers = Shipment::whereHas('shipment_journey', function($query) use ($date_from, $date_to) {
                $query->whereBetween('created_at',[$date_from,$date_to])
                    ->where('shipper_status_id', 2);
            });

            if($pickup_request_shippers->exists()){
                $pickup_request_shippers_ids = $pickup_request_shippers->pluck('user_id')->toArray();

//                $pickup_request_shippers_ids = Shipment::whereIn('id', $pickup_request_shippers)->groupBy('user_id');
//                if($pickup_request_shippers_ids->exists()){
//                    $pickup_request_shippers_ids = $pickup_request_shippers_ids->get();
//                    if($pickup_request_shippers_ids->ixists()){
//                        $pickup_request_shippers_ids = $pickup_request_shippers_ids->get();
                $shippers = User::select('id','name')->whereIn('id', $pickup_request_shippers_ids)->where('status',3)->get();
//                    }
//                }
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

        $shipper_booked = 0;
        $shipper_received = 0;
        $shipper_rev_wo_gst = 0;
        $shipper_cod = 0;
        $shipper_actual_weight = 0;
        $shipper_chargeable_weight = 0;
        if(count($shippers) > 0) {

            foreach ($shippers as $shipper) {

                if($sales_tagging == TRUE){
                    if ($search_city != null) {
                        $shipper_booked = Shipment::where('user_id', $shipper->id)
                            ->whereHas('pickup_address.city', function ($query) use ($hubs) {
                                $query->where('id', '=', $hubs[0]->id);
                            })->where('shipments.packaging_material_request', 0)->whereBetween('created_at', [$date_from,$date_to])->count();
                        $shipper_received = Shipment::where('user_id', $shipper->id)
                            ->whereHas('pickup_address.city', function ($query) use ($hubs) {
                                $query->where('id', '=', $hubs[0]->id);
                            })
                            ->whereHas('shipment_journey', function ($query) use ($date_from, $date_to) {
                                $query->whereBetween('created_at', [$date_from,$date_to])
                                    ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', 0)->count();
                        $shipper_rev_wo_gst = Shipment::where('user_id', $shipper->id)
                            ->whereHas('pickup_address.city', function ($query) use ($hubs) {
                                $query->where('id', '=', $hubs[0]->id);
                            })
                            ->whereHas('shipment_journey', function ($query) use ($date_from, $date_to) {
                                $query->whereBetween('created_at', [$date_from,$date_to])
                                    ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', 0)->sum(DB::raw('IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)'));
                        $shipper_cod = Shipment::where('user_id', $shipper->id)
                            ->whereHas('pickup_address.city', function ($query) use ($hubs) {
                                $query->where('id', '=', $hubs[0]->id);
                            })
                            ->whereHas('shipment_journey', function ($query) use ($date_from, $date_to) {
                                $query->whereBetween('created_at', [$date_from,$date_to])
                                    ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', 0)->sum('amount');
                        $shipper_actual_weight = Shipment::where('user_id', $shipper->id)
                            ->whereHas('pickup_address.city', function ($query) use ($hubs) {
                                $query->where('id', '=', $hubs[0]->id);
                            })
                            ->whereHas('shipment_journey', function ($query) use ($date_from, $date_to) {
                                $query->whereBetween('created_at', [$date_from,$date_to])
                                    ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', 0)->sum('actual_weight');
                        $shipper_chargeable_weight = Shipment::where('user_id', $shipper->id)
                            ->whereHas('pickup_address.city', function ($query) use ($hubs) {
                                $query->where('id', '=', $hubs[0]->id);
                            })
                            ->whereHas('shipment_journey', function ($query) use ($date_from, $date_to) {
                                $query->whereBetween('created_at', [$date_from,$date_to])
                                    ->where('shipper_status_id', 2);
                            })->where('shipments.packaging_material_request', 0)->sum('chargeable_weight');

                    } else {
                        $shipper_booked = Shipment::where('user_id', $shipper->id)->whereBetween('created_at', [$date_from,$date_to])->where('shipments.packaging_material_request', 0)->count();
                        $shipper_received = Shipment::where('user_id', $shipper->id)->whereHas('shipment_journey', function ($query) use ($date_from,$date_to) {
                            $query->whereBetween('created_at', [$date_from,$date_to])
                                ->where('shipper_status_id', 2);
                        })->where('shipments.packaging_material_request', 0)->count();
                        $shipper_rev_wo_gst = Shipment::where('user_id', $shipper->id)->whereHas('shipment_journey', function ($query) use ($date_from,$date_to) {
                            $query->whereBetween('created_at', [$date_from,$date_to])
                                ->where('shipper_status_id', 2);
                        })->where('shipments.packaging_material_request', 0)->sum(DB::raw('IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)'));
                        $shipper_cod = Shipment::where('user_id', $shipper->id)->whereHas('shipment_journey', function ($query) use ($date_from,$date_to) {
                            $query->whereBetween('created_at', [$date_from,$date_to])
                                ->where('shipper_status_id', 2);
                        })->where('shipments.packaging_material_request', 0)->sum('amount');
                        $shipper_actual_weight = Shipment::where('user_id', $shipper->id)->whereHas('shipment_journey', function ($query) use ($date_from,$date_to) {
                            $query->whereBetween('created_at', [$date_from,$date_to])
                                ->where('shipper_status_id', 2);
                        })->where('shipments.packaging_material_request', 0)->sum('actual_weight');
                        $shipper_chargeable_weight = Shipment::where('user_id', $shipper->id)->whereHas('shipment_journey', function ($query) use ($date_from,$date_to) {
                            $query->whereBetween('created_at', [$date_from,$date_to])
                                ->where('shipper_status_id', 2);
                        })->where('shipments.packaging_material_request', 0)->sum('chargeable_weight');

                    }

                }
                else{
                    $shipper_booked = Shipment::where('user_id', $shipper->id)->whereBetween('created_at', [$date_from,$date_to])->where('shipments.packaging_material_request', 0)->count();
                    $shipper_received = Shipment::where('user_id', $shipper->id)->whereHas('shipment_journey', function ($query) use ($date_from,$date_to) {
                        $query->whereBetween('created_at', [$date_from,$date_to])
                            ->where('shipper_status_id', 2);
                    })->where('shipments.packaging_material_request', 0)->count();
                    $shipper_rev_wo_gst = Shipment::where('user_id', $shipper->id)->whereHas('shipment_journey', function ($query) use ($date_from,$date_to) {
                        $query->whereBetween('created_at', [$date_from,$date_to])
                            ->where('shipper_status_id', 2);
                    })->where('shipments.packaging_material_request', 0)->sum(DB::raw('IFNULL(weight_charges,0) + IFNULL(cash_handling_charges,0) + IFNULL(insurance_charges,0) + IFNULL(return_charges,0) + IFNULL(fuel_surcharge,0) + IFNULL(replacement_charges,0) + IFNULL(try_and_buy_charges,0)'));
                    $shipper_cod = Shipment::where('user_id', $shipper->id)->whereHas('shipment_journey', function ($query) use ($date_from,$date_to) {
                        $query->whereBetween('created_at', [$date_from,$date_to])
                            ->where('shipper_status_id', 2);
                    })->where('shipments.packaging_material_request', 0)->sum('amount');
                    $shipper_actual_weight = Shipment::where('user_id', $shipper->id)->whereHas('shipment_journey', function ($query) use ($date_from,$date_to) {
                        $query->whereBetween('created_at', [$date_from,$date_to])
                            ->where('shipper_status_id', 2);
                    })->where('shipments.packaging_material_request', 0)->sum('actual_weight');
                    $shipper_chargeable_weight = Shipment::where('user_id', $shipper->id)->whereHas('shipment_journey', function ($query) use ($date_from,$date_to) {
                        $query->whereBetween('created_at', [$date_from,$date_to])
                            ->where('shipper_status_id', 2);
                    })->where('shipments.packaging_material_request', 0)->sum('chargeable_weight');
                }

                $shipper_avg_revenue = ($shipper_received != 0) ? $shipper_rev_wo_gst / $shipper_received : 0;
                $shipper_avg_cash_collection = ($shipper_received != 0) ? $shipper_cod / $shipper_received : 0;
                $shipper_rev_on_cash_collection = (($shipper_avg_cash_collection != 0) ? $shipper_avg_revenue / $shipper_avg_cash_collection : 0) * 100;

                //changes add columns
                $shipper_avg_actual_weight = ($shipper_actual_weight != 0)? $shipper_actual_weight/$shipper_received:0;
                $shipper_avg_rev_actual_weight = ($shipper_actual_weight != 0 && $shipper_rev_wo_gst != 0)? $shipper_rev_wo_gst/$shipper_actual_weight:0;
                $shipper_avg_chargeable_weight = ($shipper_chargeable_weight != 0)? $shipper_chargeable_weight/$shipper_received:0;
                $shipper_avg_rev_chargeable_weight = ($shipper_chargeable_weight != 0 && $shipper_rev_wo_gst != 0)? $shipper_rev_wo_gst/$shipper_chargeable_weight:0;

                //end
                $shipper_row = array();
                $shipper_row['shipper_serial'] = $serial_number_shippers;
                $shipper_row['name'] = $shipper->name;
                $shipper_row['shipper_booked'] = number_format($shipper_booked);
                $shipper_row['shipper_received'] = number_format($shipper_received);
                $shipper_row['shipper_rev_wo_gst'] = number_format($shipper_rev_wo_gst);
                $s_avg_revenue = round($shipper_avg_revenue);
                $shipper_row['shipper_avg_revenue'] = number_format($s_avg_revenue);
                $shipper_row['shipper_actual_weight'] = number_format((float)$shipper_actual_weight,2,'.','');
                $shipper_row['shipper_avg_actual_weight'] = round($shipper_avg_actual_weight);
                $shipper_row['$shipper_avg_rev_actual_weight'] = round($shipper_avg_rev_actual_weight);
                $shipper_row['shipper_chargeable_weight'] = number_format((float)$shipper_chargeable_weight,2,'.','');
                $shipper_row['shipper_avg_chargeable_weight'] = round($shipper_avg_chargeable_weight);
                $shipper_row['shipper_avg_rev_chargeable_weight'] = round($shipper_avg_rev_chargeable_weight);
                $shipper_row['shipper_cod'] = number_format($shipper_cod);

                $avg_cash_coll = round($shipper_avg_cash_collection);
                $shipper_row['shipper_avg_cc'] = number_format($avg_cash_coll);
                $avg_rev_cc = round($shipper_rev_on_cash_collection);
                $shipper_row['shipper_rcc'] = number_format($avg_rev_cc).'%';

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
                $total_shipper_avg_cash_collection += $avg_cash_coll;
//                $total_shipper_rev_on_cash_collection += $avg_rev_cc;


                $serial_number_shippers++;
            }
        }

        $total_shipper_avg_revenue = ($total_shipper_revenue_wo_gst != 0) ? $total_shipper_revenue_wo_gst / $total_shipper_received:0;
        $total_shipper_avg_actual_weight = ($total_shipper_actual_weight != 0) ? $total_shipper_actual_weight / $total_shipper_received:0;
        $total_shipper_avg_rev_actual_weight = ($total_shipper_revenue_wo_gst != 0) ? $total_shipper_revenue_wo_gst / $total_shipper_actual_weight:0;
        $total_shipper_avg_chargeable_weight = ($total_shipper_chargeable_weight != 0) ? $total_shipper_chargeable_weight / $total_shipper_received:0;
        $total_shipper_avg_rev_chargeable_weight = ($total_shipper_revenue_wo_gst != 0) ? $total_shipper_revenue_wo_gst / $total_shipper_chargeable_weight:0;
        $total_shipper_rev_on_cash_collection = ($total_shipper_avg_revenue != 0) ? $total_shipper_avg_revenue / $total_shipper_avg_cash_collection:0;

        array_multisort($shipper_sort_support_array, SORT_DESC, $sorted_shipper_array);
        foreach ($sorted_shipper_array as $shipper) {
            $details_shipper[] = $shipper;
        }
        $details_shipper[] = ['Grand Total','DSR '.$only_date, number_format($total_shipper_booked), number_format($total_shipper_received), number_format($total_shipper_revenue_wo_gst),number_format($total_shipper_avg_revenue),number_format((float)$total_shipper_actual_weight,2,'.',''),round($total_shipper_avg_actual_weight),round($total_shipper_avg_rev_actual_weight),number_format((float)$total_shipper_chargeable_weight,2,'.',''),round($total_shipper_avg_chargeable_weight),round($total_shipper_avg_rev_chargeable_weight), number_format($total_shipper_cod_collection),number_format($total_shipper_avg_cash_collection),number_format($total_shipper_rev_on_cash_collection).'%'];

        $spreadsheet = new Spreadsheet();
        $cell_st =[
            'font' =>['bold' => true],
            'alignment' =>['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders'=>['bottom' =>['style'=> \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
        ];
        $total_cell_st =[
            'font' =>['bold' => true],
            'borders'=>['bottom' =>['style'=> \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
        ];
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getDefaultColumnDimension()->setWidth(20);

        $sheet->fromArray($details,NULL,'A1',true);
        $count_hubs = count($hubs);
        $count_hub_rows = count($details);
//        $set_actual_number_format = 'G2:G'.$count_hub_rows;
//        $set_chargeable_number_format = 'J2:J'.$count_hub_rows;
//        $sheet->getStyle($set_actual_number_format)->getNumberFormat()->setFormatCode('0.00');
//        $sheet->getStyle($set_chargeable_number_format)->getNumberFormat()->setFormatCode('0.00');
        $count_hubs += 3;
        $total_shipper_rows = count($details_shipper);
        $total_shipper_rows += $count_hubs;
        $total_shipper_rows = $total_shipper_rows - 1;
//         $cellIndexShipper = Coordinate::stringFromColumnIndex($count_hubs+2);
        $shipper_cell = 'A'.$count_hubs;
        $shipper_last_cell = 'O'.$count_hubs;
        $sheet->fromArray($details_shipper,NULL,$shipper_cell,true);
//         $sheet->insertNewRowBefore(9, 8);
        $sheet->setTitle('Daily Pickup Sales Report');
//         $sheet->setCellValue('A1','S. No.');
//         $sheet->fromArray($shipper_details);
        $sheet->getStyle("A1:O1")->applyFromArray($cell_st);

        $shipper_style_cell = "A$count_hubs".":O".$count_hubs;
        $total_style_cell = "A$count_hub_rows".":O".$count_hub_rows;
        $total_shipper_style_cell = "A$total_shipper_rows".":O".$total_shipper_rows;
        $sheet->getStyle("A1:O1")->applyFromArray($cell_st);
        $sheet->getStyle($shipper_style_cell)->applyFromArray($cell_st);
        $sheet->getStyle($total_style_cell)->applyFromArray($total_cell_st);
        $sheet->getStyle($total_shipper_style_cell)->applyFromArray($total_cell_st);
        $set_shipper_actual_number_format = 'G2:G'.$total_shipper_rows;
        $set_shipper_chargeable_number_format = 'J2:J'.$total_shipper_rows;
        $sheet->getStyle($set_shipper_actual_number_format)->getNumberFormat()->setFormatCode('0.00');
        $sheet->getStyle($set_shipper_chargeable_number_format)->getNumberFormat()->setFormatCode('0.00');

        $sheet->getStyle('H1:I1')
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('FFFF00');
        $sheet->getStyle('K1:L1')
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('FFFF00');

        $shipper_color_cell1 = "H$count_hubs".":I".$count_hubs;
        $shipper_color_cell2 = "K$count_hubs".":L".$count_hubs;
        $sheet->getStyle($shipper_color_cell1)
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('FFFF00');
        $sheet->getStyle($shipper_color_cell2)
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('FFFF00');

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
            $city_name = $city['name'];
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
            $shippers = User::where('status','>=',3)->get();
            $hubs = City::select('id','name')->where('hub',1)->get();
        }else{
            $hubs = City::select('id','name')->whereIn('id',session('hubs'))->get();
            if(session('department_id') != 7){
                $shippers = User::where('status',3)->whereHas('city', function($query) {
                    $query->whereIn('hub_id', session('hubs'));
                })->get();

            }else{
                if(session('role_id') != 4){
                    $shippers = User::where('status',3)->whereIn('id', session('tagged_shippers'))->whereHas('city', function($query) {
                        $query->whereIn('hub_id', session('hubs'));
                    })->get();
                }else{
                    $shippers = User::where('status',3)->whereHas('city', function($query) {
                        $query->whereIn('hub_id', session('hubs'));
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
            $details['hubs'][$c->id] = $c->name;
            if ($shipper_filter != '') {
                $shippers = User::where('id', $shipper_filter)->whereHas('city', function ($query) use ($c) {
                    $query->where('hub_id', '=', $c->id);
                });
            } else {
                if(session('department_id') != 7){
                    $shippers = User::whereHas('city', function ($query) use ($c) {
                        $query->where('hub_id', '=', $c->id);
                    });
                }else{
                    if(session('role_id') != 4){
                        $shippers = User::whereHas('city', function ($query) use ($c) {
                            $query->where('hub_id', '=', $c->id);
                        })->whereIn('users.id', session('tagged_shippers'));
                    }else{
                        $shippers = User::whereHas('city', function ($query) use ($c) {
                            $query->where('hub_id', '=', $c->id);
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
                        $details['amount'][$s->id][$month] = number_format(Shipment::where('user_id', $s->id)->whereHas('shipment_journey', function($query) use ($thisMonth,$thisYear) {
                            $query->whereMonth('created_at', $thisMonth)
                                ->whereYear('created_at', $thisYear)
                                ->where('shipper_status_id', 2);
                        })->sum('amount'));
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
        $riders = Rider::all(['id','name']);
        $admins = Admin::all(['id','name']);
        return view('admin.reports.completed_delivery_notes_report')->with(['riders'=>$riders,'admins'=>$admins]);
    }
    public function completed_delivery_notes_list(Request $request){
        $deliveries = DeliveryNote::
        join('cities AS oc', 'delivery_notes.hub_id', '=', 'oc.id')
            ->leftjoin('delivery_note_shipments as dns','dns.delivery_note_id', '=', 'delivery_notes.id')
            ->leftjoin('shipments','shipments.id', '=', 'dns.shipment_id')
            ->join('riders', 'delivery_notes.rider_id', '=', 'riders.id')
            ->join('routes', 'delivery_notes.route_id', '=', 'routes.id')
            ->join('admins','admins.id','=','delivery_notes.admin_id')
            ->leftjoin('admins as ub','ub.id','=','delivery_notes.updated_by')
            ->leftjoin('admins as vb','vb.id','=','delivery_notes.verified_by')
            ->select(['delivery_notes.id as delivery_note','delivery_notes.id as delivery_note_id','oc.id as hub_id','oc.name as hub','riders.name as rider','routes.code as route','routes.start','routes.end','admins.name as assignee','ub.name as updated_by','delivery_notes.updated_at as updated_at','delivery_notes.delivered_shipments','delivery_notes.created_at as created_at','delivery_notes.total_cod_amount as amount','delivery_notes.shipments_count','delivery_notes.last_updated_at','vb.name as verified_by','delivery_notes.status_updated_at as status_updated','delivery_notes.status_verified_at as status_verified'])
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
                return ($deliveries->updated_at && $deliveries->verified_time)? Carbon::parse($deliveries->verified_time)->diffInDays($deliveries->updated_at) :'-';
            })
            ->addColumn('aging_create_verified',function ($deliveries){
                return ($deliveries->created_at && $deliveries->verified_time)? Carbon::parse($deliveries->verified_time)->diffInDays($deliveries->created_at) :'-';
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
        $delivery_note_details = DeliveryNote::find($delivery_note_id);
        $delivery_note_shipments = $delivery_note_details->delivery_note_shipments;
        $shipments = array();
        if($delivery_note_shipments->count() != 0){
            foreach ($delivery_note_shipments as $delivery_note_shipment){
                $shipment = Shipment::find($delivery_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Delivery Note Shipments', 'shipments' => $shipments];
        }else{
            return ['status' => 0, 'success' => 'No Delivery Note Shipments', 'shipments' => FALSE];
        }
    }
    public function completed_shipments_delivered(Request $request){
        $delivery_note_id = $request->input('delivery_note_id');
        $delivery_note_details = DeliveryNote::find($delivery_note_id);
        $delivery_note_shipments = $delivery_note_details->delivery_note_shipments()->where('status','>',1)->get();
        $shipments = array();
        if($delivery_note_shipments->count() != 0){
            foreach ($delivery_note_shipments as $delivery_note_shipment){
                $shipment = Shipment::find($delivery_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Delivery Note Shipments', 'shipments' => $shipments];
        }else{
            return ['status' => 0, 'success' => 'No Delivery Note Shipments', 'shipments' => FALSE];
        }
    }
    public function completed_delivery_note_print(Request $request)
    {
        $delivery_note_id = $request->id;
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $html = '
                <!doctype html>
                <html lang="en">
                  <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

                    <link rel="stylesheet" type="text/css" href="' . asset('app-assets/css/bootstrap.min.css') . '">

                    <title>Delivery Note</title>

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

                      td.replacement span {
                        width: 22px;
                      }

                      td.replacement span img {
                        display: block;
                        width: 100%;
                        margin: auto;
                        background: #c8c8c8;
                        border-radius: 25px;
                      }
                    </style>
                  </head>
                  <body>
                    <div>
      ';
        $delivery_note = DeliveryNote::where('id', $request->id);
        if ($delivery_note->exists()) {
            $total_shipments = 0;
            $total_cod_amount = 0;
            $shipments = DeliveryNoteShipment::where('delivery_note_id', $request->id)->select('shipment_id')->orderBy('shipment_id')->get();

            $shipment_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="color primary"><strong>S. No.</strong></td>
                            <td class="color primary"><strong>Tracking No.</strong></td>
                            <td class="color primary"><strong>Consignee Name & Phone No(s).</strong></td>
                            <td class="color primary"><strong>Consignee Address</strong></td>
                            <td class="color primary"><strong>Service Type</strong></td>
                            <td class="color primary"><strong>Collection Amount</strong></td>
                            <td class="color primary"><strong>Remarks</strong></td>
                            <td class="color primary" style="width:200px;"><strong>Receiver\'s Name</strong></td>
                            <td class="color primary" style="width:200px;"><strong>Sign</strong></td>
                          </tr>
        ';


            foreach ($shipments as $parcel) {
                $total_shipments++;
                $shipment = Shipment::find($parcel->shipment_id);

                $shipment_details_row_start = '
                          <tr>
                            <td>' . $total_shipments . '</td>
                            <td>' . $shipment->tracking_number . '</td>
                            <td>' . $shipment->consignee_name . ' | ' . $shipment->consignee_phone_number_1 . (($shipment->consignee_phone_number_2) ? (' / ' . $shipment->consignee_phone_number_2) : '') . '</td>
                            <td>' . $shipment->consignee_address . '</td>
                ';

                if ($shipment->booking_type_id == 1) {
                    $shipment_details_row_start .= '
                    <td>' . $shipment->booking_type->booking_type . '</td>
                ';
                } else if ($shipment->booking_type_id == 2) {
                    $shipment_details_row_start .= '
                    <td class="replacement"><span class="align-middle">' . $shipment->booking_type->booking_type . '</span><span class="d-inline-block align-middle float-right"><img src="' . asset('img/replacement.png') . '"></span></td>
                ';
                } else {
                    $shipment_details_row_start .= '
                    <td>' . $shipment->booking_type->booking_type . '</td>
                ';
                }

                $shipment_details_row_start .= '
                            <td>Rs ' . number_format($shipment->amount) . '</td>
                ';

                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id','!=',5)->where('reference_1_id','!=',$delivery_note_id)->select('remarks')->latest()->first();

                if ($shipment_journey) {
                    $shipment_details_row_start .= '
                            <td>' . $shipment_journey->remarks. '</td>
                    ';
                }
                else {
                    $shipment_details_row_start .= '
                            <td></td>
                    ';
                }

                $shipment_details_row_start .= '
                            <td></td>
                            <td></td>
                          </tr>
                ';

                $total_cod_amount += $shipment->amount;
                $shipment_details .= $shipment_details_row_start;
            }
            $shipment_details .= '
                        </tbody>
                      </table>
        ';
            $delivery_note_details = DeliveryNote::where('id', $request->id)->first();
            $rider = Rider::where('id', $delivery_note_details->rider_id)->first();
            $city_name = $delivery_note_details->hub->name;
            $rider_name = $rider->name;
            $category = $rider->rider_category->name;
            $route_name = $delivery_note_details->route->code . '( ' . $delivery_note_details->route->start . ' to ' . $delivery_note_details->route->end . ' )';
            $main_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo.png') . '" width="150" class="d-block mx-auto"></td>
                            <td class="text-center align-middle color primary"><strong>Delivery Note</strong></td>
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
                            <td>' . $city_name . '</td>
                          </tr>
                          <tr>
                            <td class="color secondary"><strong>Total Collection Amount</strong></td>
                            <td>Rs ' . number_format($total_cod_amount) . '</td>
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
    //completed_delivery_note
    public function customer_retention_index(){
        if (session('role_id') == 1) {
            $shippers = User::where('status','>=',3)->get();
            $hubs = City::select('id','name')->where('hub',1)->get();
        }else{
            if(session('department_id') != 7){
                $shippers = User::whereHas('city', function($query) {
                    $query->whereIn('hub_id', session('hubs'));
                })->where('status','>=',3)->get();
                $hubs = City::select('id','name')->whereIn('id',session('hubs'))->get();
            }else{
                if(session('role_id') != 4){
                    $shippers = User::whereIn('id', session('tagged_shippers'))->where('status','>=',3)->get();
                    $hubs = City::select('id','name')->whereIn('id',session('hubs'))->get();
                }else{
                    $shippers = User::whereHas('city', function($query) {
                        $query->whereIn('hub_id', session('hubs'));
                    })->where('status','>=',3)->get();
                    $hubs = City::select('id','name')->whereIn('id',session('hubs'))->get();
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
        $shippers['header'] = ['S.No','Client Name'];



        foreach($months_array as $month){

            $first_date  = Carbon::parse($month)->firstOfMonth();
            $last_date  = Carbon::parse($month)->lastOfMonth()->endOfDay();
            if($hub != null){
                if(session('department_id') != 7){
                    $details['s'][$month] = User::whereDate('activated_at','<=',$first_date)->where('status',3)->where('city_id',$hub)->count();
                    $details['e'][$month] = User::whereDate('activated_at','<=',$last_date)->where('status',3)->where('city_id',$hub)->count();
                    $details['n'][$month] = User::whereBetween('activated_at',[$first_date,$last_date])->where('status',3)->where('city_id',$hub)->count();
                }else{
                    if(session('role_id') != 4){
                        $details['s'][$month] = User::whereDate('activated_at','<=',$first_date)->where('status',3)->where('city_id',$hub)->whereIn('id', session('tagged_shippers'))->count();
                        $details['e'][$month] = User::whereDate('activated_at','<=',$last_date)->where('status',3)->where('city_id',$hub)->whereIn('id', session('tagged_shippers'))->count();
                        $details['n'][$month] = User::whereBetween('activated_at',[$first_date,$last_date])->where('status',3)->where('city_id',$hub)->whereIn('id', session('tagged_shippers'))->count();
                    }else{
                        $details['s'][$month] = User::whereDate('activated_at','<=',$first_date)->where('status',3)->where('city_id',$hub)->count();
                        $details['e'][$month] = User::whereDate('activated_at','<=',$last_date)->where('status',3)->where('city_id',$hub)->count();
                        $details['n'][$month] = User::whereBetween('activated_at',[$first_date,$last_date])->where('status',3)->where('city_id',$hub)->count();
                    }
                }
            }else{
                if(session('department_id') != 7){
                    $details['s'][$month] = User::whereDate('activated_at','<=',$first_date)->where('status',3)->count();
                    $details['e'][$month] = User::whereDate('activated_at','<=',$last_date)->where('status',3)->count();
                    $details['n'][$month] = User::whereBetween('activated_at',[$first_date,$last_date])->where('status',3)->count();
                }else{
                    if(session('role_id') != 4){
                        $details['s'][$month] = User::whereDate('activated_at','<=',$first_date)->where('status',3)->whereIn('id', session('tagged_shippers'))->count();
                        $details['e'][$month] = User::whereDate('activated_at','<=',$last_date)->where('status',3)->whereIn('id', session('tagged_shippers'))->count();
                        $details['n'][$month] = User::whereBetween('activated_at',[$first_date,$last_date])->where('status',3)->whereIn('id', session('tagged_shippers'))->count();
                    }else{
                        $details['s'][$month] = User::whereDate('activated_at','<=',$first_date)->where('status',3)->count();
                        $details['e'][$month] = User::whereDate('activated_at','<=',$last_date)->where('status',3)->count();
                        $details['n'][$month] = User::whereBetween('activated_at',[$first_date,$last_date])->where('status',3)->count();
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
                        $shippers['shipper'] = User::where('status','>=',3)->whereIn('id', session('tagged_shippers'))->get();
                    }else{
                        $shippers['shipper'] = User::whereHas('city', function($query) use ($hub) {
                            $query->where('hub_id', '=', $hub);
                        })->where('status','>=',3)->get();
                    }
                }else{

                    $shippers['shipper'] = User::whereHas('city', function($query) use ($hub) {
                        $query->where('hub_id', '=', $hub);
                    })->where('status','>=',3)->get();
                }
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
                if(session('department_id') == 7){
                    if(session('role_id') != 4){
                        $shippers['shipper'] = User::where('status','>=',3)->whereIn('id', session('tagged_shippers'))->get();
                    }else{
                        $shippers['shipper'] = User::whereHas('city', function($query) {
                            $query->whereIn('hub_id', session('hubs'));
                        })->where('status','>=',3)->get();
                    }
                }else{
                    $shippers['shipper'] = User::whereHas('city', function($query) {
                        $query->whereIn('hub_id', session('hubs'));
                    })->where('status','>=',3)->get();
                }
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
            ->leftjoin('zones as z', 'z.id', '=', 'oc.zone_id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->leftjoin('zone_class_cities as zcc', function($join){
                $join->on('z.id', '=', 'zcc.zone_id')
                ->on('dc.id', '=', 'zcc.city_id')
                ->on('zone_classification_id', '=', DB::raw('IF (shipments.shipping_mode_id IN (1, 4), 1, 2)'));
            })
            ->join('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=' , 'sps.id')
            ->leftjoin('delivery_note_shipments as ds',function($join){
                $join->on('ds.shipment_id','=','shipments.id')
                    ->where('ds.delivery_note_id','=',
                        DB::raw('(select max(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = shipments.id and delivery_note_shipments.status > 3 and  delivery_note_shipments.status != 8)')                        );
            })
            ->leftjoin('delivery_note_station_deposit_notes as dnsdn', 'ds.delivery_note_id', '=', 'dnsdn.delivery_note_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('pending_payment_shipments as pps', function ($join) {
                $join->on('pps.shipment_id', '=', 'shipments.id')
                    ->where('pps.id','=',
                        DB::raw('(select max(id) from pending_payment_shipments where pending_payment_shipments.shipment_id = shipments.id)'));
            })
            ->leftJoin('done_payment_shipments as dps', function ($join) {
                $join->on('dps.shipment_id', '=', 'shipments.id')
                    ->where('dps.id','=',
                        DB::raw('(select max(id) from done_payment_shipments where done_payment_shipments.shipment_id = shipments.id)'));
            })
            ->leftJoin('shipments_journey as dr', function ($join) {
                $join->on('dr.shipment_id', '=', 'shipments.id')
                    ->where('dr.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,20,30,36,37))'));
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
            ->select('p.product_name as category','si.description as description','shipments.id as shipment_id','shipments.tracking_number','shipments.order_id as order_id','shipments.tracking_number as tracking_number_link','u.id as account_no','u.name as shipper','ss.name as current_status','bt.booking_type as service_type','sj.created_at as arrival_date','oc.name as origin','dc.name as destination','h.name as hub','shipments.amount as s_collection_amount','sps.name as payment_status','pps.amount as p_collection_amount','shipments.actual_weight','shipments.weight_charges','shipments.cash_handling_charges','shipments.insurance_charges','shipments.return_charges','shipments.replacement_charges','shipments.fuel_surcharge','shipments.try_and_buy_charges','shipments.packaging_material_charges','pps.gst as p_gst','pps.charges as p_total_charges','pps.payable as p_net_payable','dps.amount as d_collection_amount','dps.gst as d_gst','dps.charges as d_total_charges','dps.payable as d_net_payable', 'sm.mode as shipping_mode','shipments.chargeable_weight','dr.created_at as delivered_or_returned','z.name as zone','zcc.class', 'oc.id as origin_city_id', 'dc.id as destination_city_id', 'dnsdn.station_deposit_note_id as sdn_id', 'dps.id as payment_id', 'shipments.booking_type_id', 'usi.poc', 'adsp.name as sales_person')
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
                $out_for_delivery = ShipmentsJourney::where('shipment_id',$shipment->shipment_id)->where('shipper_status_id',5)->count();
                return $out_for_delivery;
            })
            ->editColumn('insurance_charges', function($shipment){
                return number_format($shipment->insurance_charges);
            })
            ->editColumn('return_charges', function($shipment){
                return number_format($shipment->return_charges);
            })
            ->editColumn('replacement_charges', function($shipment){
                return number_format($shipment->replacement_charges);
            })
            ->editColumn('try_and_buy_charges', function($shipment){
                return number_format($shipment->try_and_buy_charges);
            })
            ->editColumn('packaging_material_charges', function($shipment){
                return number_format($shipment->packaging_material_charges);
            })
            ->editColumn('p_total_charges', function($shipment){
                return number_format($shipment->p_total_charges);
            })
            ->editColumn('d_total_charges', function($shipment){
                return number_format($shipment->d_total_charges);
            })
            ->editColumn('p_net_payable', function($shipment){
                return number_format($shipment->p_net_payable);
            })
            ->editColumn('d_net_payable', function($shipment){
                return number_format($shipment->d_net_payable);
            })
            ->editColumn('d_gst', function($shipment){
                return number_format($shipment->d_gst);
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
                if($sale->p_gst != null){
                    $gst = $sale->p_gst;
                }else if($sale->d_gst != null){
                    $gst = $sale->d_gst;
                }
                return number_format((float)$gst);
            })
            ->editColumn('p_total_charges',function($sale){
                $total = '';
                if($sale->p_total_charges != null){
                    $total = $sale->p_total_charges;
                }else if($sale->d_total_charges != null){
                    $total = $sale->d_total_charges;
                }
                return number_format((float)$total);
            })
            ->addColumn('estimated_charges',function($sale){
                $estimated = '';
                $estimated = (($sale->weight_charges != null)? $sale->weight_charges:0) + (($sale->cash_handling_charges != null)? $sale->cash_handling_charges:0) + (($sale->insurance_charges != null)? $sale->insurance_charges:0) + (($sale->insurance_charges != null)? $sale->insurance_charges:0) + (($sale->return_charges != null)? $sale->return_charges:0) + (($sale->replacement_charges != null)? $sale->replacement_charges:0) + (($sale->fuel_surcharge != null)? $sale->fuel_surcharge:0) + (($sale->try_and_buy_charges != null)? $sale->try_and_buy_charges:0) + (($sale->packaging_material_charges != null)? $sale->packaging_material_charges:0);
                return number_format((float)$estimated);
            })
            ->editColumn('p_net_payable',function($sale){
                $payable = '';
                if($sale->p_net_payable != null){
                    $payable = $sale->p_net_payable;
                }else if($sale->d_net_payable != null){
                    $payable = $sale->d_net_payable;
                }
                return number_format((float)$payable);
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
            $sales_persons = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name'])->where('ar.department_id', 7)->get();
            $hubs = City::select('id','name')->where('hub',1)->get();
        }else{
            if(session('department_id') != 7){
                $sales_persons = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name'])->where('ar.department_id', 7)->get();
                $hubs = City::select('id','name')->whereIn('id',session('hubs'))->get();
            }else{
                if(session('role_id') != 4){
                    $sales_persons = Admin::where('id', Auth::id())->select('id', 'name')->get();
                    $hubs = City::select('id','name')->whereIn('id',session('hubs'))->get();
                }else{
                    $sales_persons = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name'])->where('ar.department_id', 7)->get();
                    $hubs = City::select('id','name')->whereIn('id',session('hubs'))->get();
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
        $number_of_days = $start_date->diffInDays($current_date);
        $dates = [];

        for($d = $start_date; $d->lte($current_date); $d->addDay()) {
            $dates[] = $d->format('Y-m-d');
        }

        if($sales_person_filter != null){
            $sales_person = Admin::where('id', $sales_person_filter)->get();

        }else{
            if(session('role_id') == 1){
                $sales_person = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name'])->where('ar.department_id', 7)->get();
            }else{
                if(session('department_id') != 7){
                    $sales_person = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name'])->where('ar.department_id', 7)->get();
                }else{
                    if(session('role_id') != 4){
                        $sales_person = Admin::where('id', Auth::id())->get();
                    }else{
                        $sales_person = Admin::join('admin_roles as ar', 'admins.role_id', '=', 'ar.id')->select(['admins.id', 'admins.name'])->where('ar.department_id', 7)->get();
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

        $details['header'] = ['Sales Persons', 'Client Name' ];
//        $details['subheader'] = ['Parcels', 'Weight','Collection Amount','Revenue' ];

        foreach ($sales_person as $person){
            $sales_persons_data[$person->id]['name'] = $person->name;

            $tagged_shippers = SalePersonTag::where('admin_id', $person->id)->where('status', 0)->select('user_id')->get();
            foreach ($tagged_shippers as $shipper){
                if($hub != null){

                    $user = User::whereHas('city',function($query) use($hub){
                        $query->where('hub_id',$hub);
                    })->where('id', $shipper->user_id)->first();
                }else{

                    $user = User::find($shipper->user_id);
                }
                if($user){

                    $sales_persons_data[$person->id]['shipper'][$user->id] = $user->name;
                    foreach ($dates as $date){
                        if($hub != null){
                            $sum = Shipment::whereHas('shipment_journey', function($query) use ($date) {
                                $query->whereDate('created_at',$date)
                                    ->where('shipper_status_id', 2);
                            })->whereHas('pickup_address.city', function ($query) use ($hub) {
                                $query->where('hub_id', '=', $hub);
                            })->where('shipments.user_id', $user->id)->count();
                        }else{
                            $sum = Shipment::whereHas('shipment_journey', function($query) use ($date) {
                                $query->whereDate('created_at',$date)
                                    ->where('shipper_status_id', 2);
                            })->where('shipments.user_id', $user->id)->count();
                        }


                        $sales_persons_data[$person->id]['pickups'][$user->id][] = $sum;

                        $date_sums[$date] += $sum;
                        $overall_sum += $sum;
                    }

                    $sum_of_pickups = array_sum($sales_persons_data[$person->id]['pickups'][$user->id]);
                    array_push($sales_persons_data[$person->id]['pickups'][$user->id],$sum_of_pickups);
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
        $pickup_col_index = 3;
        foreach ($sales_persons_data as $sales_persons) {

            $sheet->setCellValue('A'.$admin_index, $sales_persons['name']);

            if(!empty($sales_persons['shipper'])) {
                foreach ($sales_persons['shipper'] as $key => $person) {

                    $sheet->setCellValue('B' . $shipper_index, $person);

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

                    $pickup_col_index = 3;
                    $pickup_index++;
                }

            }

        }
        $pickup_index += 2;
        $date_sum_col_index = 3;
        $date_sum_index = $pickup_index;
        $sheet->setCellValue('A'.$date_sum_index, "Grand Total");
        foreach ($date_sums as $date => $sum) {
            $cellIndex = Coordinate::stringFromColumnIndex($date_sum_col_index);
            $sheet->setCellValue($cellIndex . $date_sum_index, $sum);

            $date_sum_col_index++;
        }

        $cellIndexcol1 = 3;
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
        $negative = PendingPaymentShipment::leftjoin('shipments as s','s.id','=','pending_payment_shipments.shipment_id')
            ->leftjoin('users as u','u.id','=','s.user_id')
            ->select('u.id as account_no','u.name as name','u.phone as phone','pending_payment_shipments.amount as amount','pending_payment_shipments.charges as charges','pending_payment_shipments.payable as payable')
            ->where('payable','<',0);
        $datatable = Datatables::of($negative)
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->editColumn('charges', function($shipment){
                return number_format($shipment->charges);
            })
            ->editColumn('payable', function($shipment){
                return number_format($shipment->payable);
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
        $call_verification_report = DeliveryNote::leftjoin('delivery_note_shipments as dns', 'dns.delivery_note_id', '=', 'delivery_notes.id')
            ->leftjoin('shipments as s', 's.id', '=', 'dns.shipment_id')
            ->leftjoin('admins as ad','ad.id','=','delivery_notes.verified_by')
            ->join('shipments_journey as sj', function($join){
                $join->on('sj.shipment_id', '=', 'dns.shipment_id')
                    ->where('sj.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = dns.shipment_id and shipments_journey.reference_1_id = dns.delivery_note_id and shipments_journey.verification = 1)'));
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
        $hubs = City::where('hub',1)->select('id','name')->get();
        $heads = PettyCashAccountHead::select('id', 'name')->get();
        $titles = PettyCashAccountTitle::select('id', 'name')->get();
        return view('admin.reports.petty_cash_statement')->with(['hubs' => $hubs, 'heads' => $heads, 'titles' => $titles]);
    }

    public function petty_cash_statements_list(Request $request){
        $petty = PettyCashStatementDetail::join('petty_cash_statements as pcs','pcs.id','=','petty_cash_statement_details.petty_cash_statement_id')
            ->join('cities as dc','dc.id','=', 'petty_cash_statement_details.hub_id')
            ->join('cities as h','h.id','=', 'pcs.hub_id')
            ->join('admins as cb','cb.id','=', 'pcs.created_by')
            ->leftjoin('admins as sub', 'sub.id', '=', 'petty_cash_statement_details.updated_by')
            ->leftjoin('petty_cash_account_heads as pch', 'pch.id','=','petty_cash_statement_details.account_head_id')
            ->leftjoin('petty_cash_account_titles as pct', 'pct.id','=','petty_cash_statement_details.account_title_id')
            ->select('pcs.id as statement_id','pcs.id as statement_link','dc.name as entry_city','petty_cash_statement_details.date as entry_date','pch.name as account_head','pct.name as account_title','petty_cash_statement_details.expense_details','petty_cash_statement_details.amount','petty_cash_statement_details.reference_no as entry_reference_no','petty_cash_statement_details.remarks','petty_cash_statement_details.status','pcs.reference_no as statement_reference_no','h.name as hub_name','cb.name as created_by','pcs.created_at');
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
        if ($search_date = $request->get('search_date_created')) {
            $petty->whereDate('pcs.created_at', $search_date);
        }
        if($head = $request->get('search_head')){
            $petty->where('pch.id','=',$head);
        }
        if($title = $request->get('search_title')){
            $petty->where('pct.id','=',$title);
        }
        return $petty->make(true);
    }

	public function fake_status_index(){
        $riders = Rider::all(['id','name']);
        $hubs = City::where('hub',1)->select('id','name')->get();
        return view('admin.reports.fake_statuses_report')->with(['riders' => $riders, 'hubs' => $hubs]);
    }

    public function fake_status_list(request $request){
        $delivery_note = DeliveryNote::join('delivery_note_shipments as dns','dns.delivery_note_id', '=', 'delivery_notes.id')
            ->leftjoin('riders as r', 'r.id', '=', 'delivery_notes.rider_id')
            ->leftjoin('cities as c', 'c.id', '=', 'r.city_id')
            ->select('r.name as rider_name', 'c.name as rider_city', 'delivery_notes.id as delivery_note_id', 'delivery_notes.created_at', 'delivery_notes.status_verified_at as verified_at', 'delivery_notes.shipments_count as total_shipments', 'delivery_notes.delivered_shipments as delivered_shipments', DB::raw('(select count(shipment_id) from delivery_note_shipments where delivery_note_shipments.delivery_note_id = delivery_notes.id and delivery_note_shipments.fake_status = 1) as shipment_fake_status'))
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
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatables->whereBetween('delivery_notes.created_at', [$from,$to]);
        }
        return $datatables->make(true);
    }

    public function fake_status_shipments_total(Request $request){
        $delivery_note_id = $request->input('delivery_note_id');
        $delivery_note_details = DeliveryNote::find($delivery_note_id);
        $delivery_note_shipments = $delivery_note_details->delivery_note_shipments()->get();
        $shipments = array();
        if($delivery_note_shipments->count() != 0){
            foreach ($delivery_note_shipments as $delivery_note_shipment){
                $shipment = Shipment::find($delivery_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Delivery Note Shipments', 'shipments' => $shipments];
        }else{
            return ['status' => 0, 'success' => 'No Delivery Note Shipments', 'shipments' => FALSE];
        }
    }
    public function fake_status_shipments_undelivered(Request $request){
        $delivery_note_id = $request->input('delivery_note_id');
        $delivery_note_details = DeliveryNote::find($delivery_note_id);
        $delivery_note_shipments = $delivery_note_details->delivery_note_shipments()->where('status','=',1)->get();
        $shipments = array();
        if($delivery_note_shipments->count() != 0){
            foreach ($delivery_note_shipments as $delivery_note_shipment){
                $shipment = Shipment::find($delivery_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Delivery Note Shipments', 'shipments' => $shipments];
        }else{
            return ['status' => 0, 'success' => 'No Delivery Note Shipments', 'shipments' => FALSE];
        }
    }

    public function fake_status_shipments(Request $request){
        $delivery_note_id = $request->input('delivery_note_id');
        $delivery_note_details = DeliveryNote::find($delivery_note_id);
        $delivery_note_shipments = $delivery_note_details->delivery_note_shipments()->where('fake_status','=',1)->get();
        $shipments = array();
        if($delivery_note_shipments->count() != 0){
            foreach ($delivery_note_shipments as $delivery_note_shipment){
                $shipment = Shipment::find($delivery_note_shipment->shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Delivery Note Shipments', 'shipments' => $shipments];
        }else{
            return ['status' => 0, 'success' => 'No Delivery Note Shipments', 'shipments' => FALSE];
        }
    }

	public function debriefing_index() {
        $hubs = City::where('hub', 1)->select('id','name')->get();
        $zones = Zone::all();

        return view('admin.reports.debriefing_report')->with(['hubs' => $hubs, 'zones' => $zones]);
    }

    private function debriefing_data($date, $hub, $zone, $export = FALSE) {
        $settings = GlobalSettings::where('type', 'debriefing_report_arrival_cut_off_time')->first();

        if ($settings) {
            $arrival_cut_off_time = $settings->setting_value;
        }
        else {
            $arrival_cut_off_time = 12;
        }

        $settings = GlobalSettings::where('type', 'debriefing_report_day_cut_off_time')->first();

        if ($settings) {
            $day_cut_off_time = $settings->setting_value;
        }
        else {
            $day_cut_off_time = 12;
        }

        $hubs = City::where('hub', 1)->select('id','name');

        if ($hub) {
            $hubs = $hubs->where('id', '=', $hub);
        }

        if ($zone) {
            $hubs = $hubs->where('zone_id', '=', $zone);
        }

        if ($hubs->exists()) {
            $hubs = $hubs->get();

            if ($date) {
                $from = Carbon::parse($date)->addHour($day_cut_off_time)->toDateTimeString();
                $to = Carbon::parse($date)->addDay()->addHour($day_cut_off_time)->subSecond()->toDateTimeString();
            }
            else {
                $from = Carbon::today()->addHour($day_cut_off_time)->toDateTimeString();
                $to = Carbon::tomorrow()->addHour($day_cut_off_time)->subSecond()->toDateTimeString();
            }

            $types = ['status_not_updated', 'delivered', 'delivery_unsucessful', 'on_hold', 'confirmation_pending', 'lost', 'confirm', 'correct_status', 'fake_status', 'delivery_note_pending', 'delivery_tomorrow'];

            $counts = array();

            if ($export) {
                $shipments = array();
            }

            foreach ($hubs as $hub) {
                foreach ($types as $type) {
                    $rows = City::join('shipments as s', 'cities.id', '=', 's.consignee_city_id');

                    if ($type == 'status_not_updated') {
                        $rows = $rows->join('user_shipping_infos as usi', 'usi.id', '=', 's.pickup_address_id')
                        ->join('cities as pc', 'usi.city_id', '=', 'pc.id')
                        ->join('zone_class_cities as zcc', function($join) {
                            $join->on('pc.zone_id', '=', 'zcc.zone_id')
                            ->on('s.consignee_city_id', '=', 'zcc.city_id');
                        })
                        ->leftjoin('delivery_note_shipments as dns', function($join) {
                            $join->on('s.id', '=', 'dns.shipment_id')
                            ->where('dns.delivery_note_id', '=', DB::raw('(select max(dns.delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = s.id)'));
                        })
                        ->leftjoin('delivery_notes as dn', function($join) {
                            $join->on('dns.delivery_note_id', '=', 'dn.id')
                            ->whereDate('dn.created_at', 'shipments_journey.created_at');
                        })
                        ->join('shipments_journey as sj', function($join) use ($arrival_cut_off_time) {
                            $join->on('s.id', '=', 'sj.shipment_id')
                            ->where('sj.id', '=', DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = s.id and shipments_journey.verification = 1 and (shipments_journey.shipper_status_id = 7 or (usi.city_id = s.consignee_city_id or zcc.class in (0, 1)) and dns.delivery_note_id is null and (shipments_journey.shipper_status_id in (2, 4) and hour(shipments_journey.created_at) < ' . $arrival_cut_off_time . ')))'));
                        });
                    }
                    else if ($type == 'delivered') {
                        $rows = $rows->join('shipments_journey as sj', function($join) {
                            $join->on('s.id', '=', 'sj.shipment_id')
                        ->where('sj.id', '=', DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = s.id and shipments_journey.verification = 1 and shipments_journey.shipper_status_id IN (14, 30, 36, 37))'));
                        });
                    }
                    else if ($type == 'delivery_unsucessful') {
                        $rows = $rows->join('shipments_journey as sj', function($join) {
                            $join->on('s.id', '=', 'sj.shipment_id')
                        ->where('sj.id', '=', DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = s.id and shipments_journey.verification = 1 and shipments_journey.shipper_status_id = 8)'));
                        });
                    }
                    else if ($type == 'on_hold') {
                        $rows = $rows->join('shipments_journey as sj', function($join) {
                            $join->on('s.id', '=', 'sj.shipment_id')
                        ->where('sj.id', '=', DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = s.id and shipments_journey.verification = 1 and shipments_journey.shipper_status_id IN (9, 10, 11, 15))'));
                        });
                    }
                    else if ($type == 'confirmation_pending') {
                        $rows = $rows->join('shipments_journey as sj', function($join) {
                            $join->on('s.id', '=', 'sj.shipment_id')
                        ->where('sj.id', '=', DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = s.id and shipments_journey.verification = 1 and shipments_journey.shipper_status_id = 12)'));
                        });
                    }
                    else if ($type == 'lost') {
                        $rows = $rows->join('shipments_journey as sj', function($join) {
                            $join->on('s.id', '=', 'sj.shipment_id')
                        ->where('sj.id', '=', DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = s.id and shipments_journey.verification = 1 and shipments_journey.shipper_status_id = 18)'));
                        });
                    }
                    else if ($type == 'confirm') {
                        $rows = $rows->join('shipments_journey as sj', function($join) {
                            $join->on('s.id', '=', 'sj.shipment_id')
                        ->where('sj.id', '=', DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = s.id and shipments_journey.verification = 1 and shipments_journey.shipper_status_id = 20 and shipments_journey.reference_1_id is not null)'));
                        });
                    }
                    else if ($type == 'correct_status') {
                        $rows = $rows->join('delivery_note_shipments as dns', function($join) {
                            $join->on('s.id', '=', 'dns.shipment_id')
                            ->where('dns.delivery_note_id', '=', DB::raw('(select max(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = s.id and fake_status = 0)'));
                        })
                        ->join('delivery_notes as dn', function($join) {
                            $join->on('dns.delivery_note_id', '=', 'dn.id')
                            ->where('dn.status', '=', 1);
                        });
                    }
                    else if ($type == 'fake_status') {
                        $rows = $rows->join('delivery_note_shipments as dns', function($join) {
                            $join->on('s.id', '=', 'dns.shipment_id')
                            ->where('dns.delivery_note_id', '=', DB::raw('(select max(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = s.id and fake_status = 1)'));
                        })
                        ->join('delivery_notes as dn', function($join) {
                            $join->on('dns.delivery_note_id', '=', 'dn.id')
                            ->where('dn.status', '=', 1);
                        });
                    }
                    else if ($type == 'delivery_note_pending') {
                        $rows = $rows->join('shipments_journey as sj', function($join) {
                            $join->on('s.id', '=', 'sj.shipment_id')
                        ->where('sj.id', '=', DB::raw('(select max(isj.id) from shipments_journey as isj left join shipments_journey as isjj on isj.shipment_id = isjj.shipment_id and isj.reference_1_id = isjj.reference_1_id and isj.id != isjj.id where isj.shipment_id = s.id and isj.verification = 1 and isj.shipper_status_id = 5 and isjj.id is null)'));
                        });
                    }
                    else if ($type == 'delivery_tomorrow') {
                        $rows = $rows->join('user_shipping_infos as usi', 'usi.id', '=', 's.pickup_address_id')
                        ->join('cities as pc', 'usi.city_id', '=', 'pc.id')
                        ->join('zone_class_cities as zcc', function($join) {
                            $join->on('pc.zone_id', '=', 'zcc.zone_id')
                            ->on('s.consignee_city_id', '=', 'zcc.city_id');
                        })
                        ->leftjoin('delivery_note_shipments as dns', function($join) {
                            $join->on('s.id', '=', 'dns.shipment_id')
                            ->where('dns.delivery_note_id', '=', DB::raw('(select max(dns.delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = s.id)'));
                        })
                        ->leftjoin('delivery_notes as dn', function($join) {
                            $join->on('dns.delivery_note_id', '=', 'dn.id')
                            ->whereDate('dn.created_at', 'shipments_journey.created_at');
                        })
                        ->join('shipments_journey as sj', function($join) use ($arrival_cut_off_time) {
                            $join->on('s.id', '=', 'sj.shipment_id')
                            ->where('sj.id', '=', DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = s.id and shipments_journey.verification = 1 and ((usi.city_id = s.consignee_city_id and shipments_journey.shipper_status_id = 2) or (usi.city_id != s.consignee_city_id and shipments_journey.shipper_status_id = 4)) and (hour(shipments_journey.created_at) >= ' . $arrival_cut_off_time . ' or zcc.class in (2, 3)) and dns.delivery_note_id is null)'));
                        });
                    }

                    $rows = $rows->select('s.tracking_number')->where('cities.hub_id', $hub->id);

                    if ($type != 'correct_status' && $type != 'fake_status') {
                        $rows = $rows->whereBetween('sj.created_at', [$from, $to]);
                    }
                    else {
                        $rows = $rows->whereBetween('dn.created_at', [$from, $to]);
                    }

                    if ($rows->exists()) {
                        $rows = $rows->groupBy('s.id');

                        $rows = $rows->get();

                        $counts[$hub->name][$type] = $rows->count();

                        if ($type != 'correct_status' && $type != 'fake_status') {
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
                            $shipments[$type][$hub->name] = array();

                            foreach ($rows as $row) {
                                $shipments[$type][$hub->name][] = $row->tracking_number;
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

        $details[] = ['Hubs', 'Status Not Updated', 'Delivered', 'Delivery Unsuccessful', 'On Hold', 'Confirmation Pending', 'Lost', 'Confirm', 'Correct Status', 'Fake Status', 'Total', 'Ratio', 'Delivery Note Pending', 'Total', 'Ratio', 'Delivery Tomorrow', 'Grand Total', 'Ratio'];

        $result = $this->debriefing_data($date, $hub, $zone, TRUE);

        if ($result['status'] == 0) {
            $types = ['status_not_updated', 'delivered', 'delivery_unsucessful', 'on_hold', 'confirmation_pending', 'lost', 'confirm', 'correct_status', 'fake_status', 'total_1', 'total_1_ratio', 'delivery_note_pending', 'total_2', 'total_2_ratio', 'delivery_tomorrow', 'grand_total', 'grand_total_ratio'];

            $type_names = ['status_not_updated' => 'Status Not Updated', 'delivered' => 'Delivered', 'delivery_unsucessful' => 'Delivery Unsuccessful', 'on_hold' => 'On Hold', 'confirmation_pending' => 'Confirmation Pending', 'lost' => 'Lost', 'confirm' => 'Confirm', 'correct_status' => 'Correct Status', 'fake_status' => 'Fake Status', 'total_1' => 'Total', 'total_1_ratio' => 'Ratio', 'delivery_note_pending' => 'Delivery Note Pending', 'total_2' => 'Total', 'total_2_ratio' => 'Ratio', 'delivery_tomorrow' => 'Delivery Tomorrow', 'grand_total' => 'Grand Total', 'grand_total_ratio' => 'Ratio'];

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
            $spreadsheet->getActiveSheet()->getStyle('I')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $spreadsheet->getActiveSheet()->getStyle('J')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $spreadsheet->getActiveSheet()->getStyle('K')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $spreadsheet->getActiveSheet()->getStyle('L')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
            $spreadsheet->getActiveSheet()->getStyle('M')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $spreadsheet->getActiveSheet()->getStyle('N')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $spreadsheet->getActiveSheet()->getStyle('O')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
            $spreadsheet->getActiveSheet()->getStyle('P')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $spreadsheet->getActiveSheet()->getStyle('Q')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $spreadsheet->getActiveSheet()->getStyle('R')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);

            $spreadsheet->getActiveSheet()->getStyle('B')->getFont()->getColor()->setARGB('FFFF0000');
            $spreadsheet->getActiveSheet()->getStyle('J')->getFont()->getColor()->setARGB('FFFF0000');

            $spreadsheet->getActiveSheet()->setTitle('Overall')->fromArray($details, NULL);

            foreach ($result['shipments'] as $type => $hubs) {
                $details = array();

                if (count($hubs) > 1) {
                    foreach ($hubs as $hub => $tracking_numbers) {
                        $detail = array();

                        $detail[] = $hub;

                        foreach ($tracking_numbers as $tracking_number) {
                            $detail[] = $tracking_number;
                        }

                        $details[] = $detail;
                    }

                    $details = array_map(null, ...$details);
                }
                else {
                    foreach ($hubs as $hub => $tracking_numbers) {
                        $details[] = [$hub];

                        foreach ($tracking_numbers as $tracking_number) {
                            $details[] = [$tracking_number];
                        }
                    }
                }

                $spreadsheet->createSheet()->setTitle($type_names[$type]);

                $spreadsheet->setActiveSheetIndexByName($type_names[$type]);

                for ($counter = 1; $counter <= count($hubs); $counter++) {
                    $column_name = Coordinate::stringFromColumnIndex($counter);

                    $spreadsheet->getActiveSheet()->getStyle($column_name)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);

                    $spreadsheet->getActiveSheet()->getColumnDimension($column_name)->setWidth(15);
                }

                $spreadsheet->getActiveSheet()->setTitle($type_names[$type])->fromArray($details);
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

        $cities = City::select('id','name')->where('hub',1)->get();
        return view('admin.reports.cargo_returns_shipment_report')->with('cities', $cities);
    }
    public function cargo_returns_shipment_list(Request $request){
        $cargo_returns_Shipment = Shipment::join('shipments_journey as sj', function ($join) {
            $join->on('sj.shipment_id' , '=', 'shipments.id')
                    ->where('sj.id' , '=', DB::raw('(select max(id) from shipments_journey where shipment_id = shipments.id and shipments_journey.shipper_status_id in (20, 30, 37))'));
            })
            ->leftjoin('shipments_journey as sjc', function ($join) {
                $join->on('sjc.shipment_id' , '=', 'shipments.id')
                    ->where('sjc.id' , '=', DB::raw('(select max(id) from shipments_journey where shipment_id = shipments.id and shipments_journey.shipper_status_id in (21, 26, 32))'));
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
        $cities = City::where('status', 1)->get();
        return view('admin.reports.return_reattempt_ratio')->with(['cities' => $cities]);
    }

    public function return_reattempt_ratio_list(Request $request){
        $shipments = ReturnReattemptRatio::join('shipments','shipments.id','=','return_reattempt_ratios.shipment_id')
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
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipments_journey as journey', function ($join) {
                $join->on('journey.shipment_id', '=', 'shipments.id')
                    ->where('journey.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->select(['shipments.id as shId','shipments.tracking_number','shipments.tracking_number as tracking_number_link','u.name as shipper','ss.name as current_status','bt.booking_type as service_type','sj.created_at as arrival','oc.name as origin','dc.name as destination','h.name as hub','shipments.amount','journey.created_at as current_status_date', 'shipments.booking_type_id', 'return_reattempt_ratios.return_confirm_date','return_reattempt_ratios.created_at as reattempt_date']);
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
        $payments = DonePaymentShipment::join('shipments as s','s.id', '=', 'done_payment_shipments.shipment_id')->select(['s.tracking_number as tracking_number', 's.actual_weight as actual_weight', 's.cash_handling_charges as cash_handling_charges','s.insurance_charges as insurance_charges','s.return_charges as return_charges','s.fuel_surcharge as fuel_surcharge','s.replacement_charges as replacement_charges','s.packaging_material_charges as packaging_material_charges', 'done_payment_shipments.done_payment_id as payment_id', 'done_payment_shipments.gst as gst', 'done_payment_shipments.amount as amount', 'done_payment_shipments.payable as total_payable', 'done_payment_shipments.type as status']);
        $datatable = Datatables::of($payments)
            ->addColumn('id_padded', function ($shipments) {
                return str_pad($shipments->payment_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('payment_id_link', function($shipments) {
                return '<button class="btn btn-sm btn-outline-info align-middle"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($shipments->payment_id, 6, '0', STR_PAD_LEFT) . '</span></button>';
            })
            ->editColumn('cash_handling_charges', function ($shipments){
                return number_format($shipments->cash_handling_charges);
            })
            ->editColumn('insurance_charges', function ($shipments){
                return number_format($shipments->insurance_charges);
            })
            ->editColumn('return_charges', function ($shipments){
                return number_format($shipments->return_charges);
            })
            ->editColumn('fuel_surcharge', function ($shipments){
                return number_format($shipments->fuel_surcharge);
            })
            ->editColumn('replacement_charges', function ($shipments){
                return number_format($shipments->replacement_charges);
            })
            ->editColumn('packaging_material_charges', function ($shipments){
                return number_format($shipments->packaging_material_charges);
            })
            ->editColumn('gst', function ($shipments){
                return number_format($shipments->gst);
            })
            ->editColumn('amount', function ($shipments){
                return number_format($shipments->amount);
            })
            ->editColumn('total_payable', function ($shipments){
                return number_format($shipments->total_payable);
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
        $shippers = User::whereIn('status',[3,4])->select('id','name')->get();
        $cities = City::all('id','name');
        $hubs = City::where('hub',1)->select('id','name')->get();
        $statuses = ShipmentStatus::whereNotIn('id',[1,17])->get();
        return view('admin.reports.revenue')->with(['shippers'=>$shippers,'cities'=>$cities,'hubs'=>$hubs,'statuses'=>$statuses]);
    }
    public function revenue_list(Request $request){

        $sales = Shipment::join('users as u','u.id','=','shipments.user_id')
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
                    ->on('zone_classification_id', '=', DB::raw('IF (shipments.shipping_mode_id IN (1, 4), 1, 2)'));
            })
            ->join('shipping_modes as sm', 'sm.id', '=', 'shipments.shipping_mode_id')
            ->leftjoin('shipment_payment_status as sps', 'shipments.payment_status_id', '=' , 'sps.id')
            ->leftjoin('delivery_note_shipments as ds',function($join){
                $join->on('ds.shipment_id','=','shipments.id')
                    ->where('ds.delivery_note_id','=',
                        DB::raw('(select max(delivery_note_id) from delivery_note_shipments where delivery_note_shipments.shipment_id = shipments.id and delivery_note_shipments.status > 3 and  delivery_note_shipments.status != 8)'));
            })
            ->leftjoin('delivery_note_station_deposit_notes as dnsdn', 'ds.delivery_note_id', '=', 'dnsdn.delivery_note_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('pending_payment_shipments as pps', function ($join) {
                $join->on('pps.shipment_id', '=', 'shipments.id')
                    ->where('pps.id','=',
                        DB::raw('(select max(id) from pending_payment_shipments where pending_payment_shipments.shipment_id = shipments.id)'));
            })
            ->leftJoin('done_payment_shipments as dps', function ($join) {
                $join->on('dps.shipment_id', '=', 'shipments.id')
                    ->where('dps.id','=',
                        DB::raw('(select max(id) from done_payment_shipments where done_payment_shipments.shipment_id = shipments.id)'));
            })
            ->leftJoin('shipments_journey as dr', function ($join) {
                $join->on('dr.shipment_id', '=', 'shipments.id')
                    ->where('dr.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id In(14,20,30,36,37))'));
            })
            ->select('shipments.tracking_number','shipments.order_id as order_id','shipments.tracking_number as tracking_number_link','u.id as account_no','u.name as shipper','ss.name as current_status','bt.booking_type as service_type','sj.created_at as arrival_date','oc.name as origin','dc.name as destination','h.name as hub','shipments.amount as s_collection_amount','sps.name as payment_status','pps.amount as p_collection_amount','shipments.actual_weight','shipments.weight_charges','shipments.cash_handling_charges','shipments.insurance_charges','shipments.return_charges','shipments.replacement_charges','shipments.fuel_surcharge','shipments.try_and_buy_charges','shipments.packaging_material_charges','pps.gst as p_gst','pps.charges as p_total_charges','pps.payable as p_net_payable','dps.amount as d_collection_amount','dps.gst as d_gst','dps.charges as d_total_charges','dps.payable as d_net_payable', 'sm.mode as shipping_mode','shipments.chargeable_weight','dr.created_at as delivered_or_returned','z.name as zone','zcc.class', 'oc.id as origin_city_id', 'dc.id as destination_city_id', 'dnsdn.station_deposit_note_id as sdn_id', 'dps.id as payment_id', 'shipments.booking_type_id', 'usi.poc')
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
                return number_format($shipment->insurance_charges);
            })
            ->editColumn('return_charges', function($shipment){
                return number_format($shipment->return_charges);
            })
            ->editColumn('replacement_charges', function($shipment){
                return number_format($shipment->replacement_charges);
            })
            ->editColumn('try_and_buy_charges', function($shipment){
                return number_format($shipment->try_and_buy_charges);
            })
            ->editColumn('packaging_material_charges', function($shipment){
                return number_format($shipment->packaging_material_charges);
            })
            ->editColumn('p_total_charges', function($shipment){
                return number_format($shipment->p_total_charges);
            })
            ->editColumn('d_total_charges', function($shipment){
                return number_format($shipment->d_total_charges);
            })
            ->editColumn('p_net_payable', function($shipment){
                return number_format($shipment->p_net_payable);
            })
            ->editColumn('d_net_payable', function($shipment){
                return number_format($shipment->d_net_payable);
            })
            ->editColumn('d_gst', function($shipment){
                return number_format($shipment->d_gst);
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
                if($sale->p_gst != null){
                    $gst = $sale->p_gst;
                }else if($sale->d_gst != null){
                    $gst = $sale->d_gst;
                }
                return number_format((float)$gst);
            })
            ->editColumn('p_total_charges',function($sale){
                $total = '';
                if($sale->p_total_charges != null){
                    $total = $sale->p_total_charges;
                }else if($sale->d_total_charges != null){
                    $total = $sale->d_total_charges;
                }
                return number_format((float)$total);
            })
            ->addColumn('estimated_charges',function($sale){
                $estimated = '';
                $estimated = (($sale->weight_charges != null)? $sale->weight_charges:0) + (($sale->cash_handling_charges != null)? $sale->cash_handling_charges:0) + (($sale->insurance_charges != null)? $sale->insurance_charges:0) + (($sale->insurance_charges != null)? $sale->insurance_charges:0) + (($sale->return_charges != null)? $sale->return_charges:0) + (($sale->replacement_charges != null)? $sale->replacement_charges:0) + (($sale->fuel_surcharge != null)? $sale->fuel_surcharge:0) + (($sale->try_and_buy_charges != null)? $sale->try_and_buy_charges:0) + (($sale->packaging_material_charges != null)? $sale->packaging_material_charges:0);
                return number_format((float)$estimated);
            })
            ->editColumn('p_net_payable',function($sale){
                $payable = '';
                if($sale->p_net_payable != null){
                    $payable = $sale->p_net_payable;
                }else if($sale->d_net_payable != null){
                    $payable = $sale->d_net_payable;
                }
                return number_format((float)$payable);
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
}

