<?php

namespace App\Http\Controllers\Admins;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Controllers\ShipmentOpenBoxJourneyController;
use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\Admin\ReturnNoteShipment;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\Rider;
use App\Http\Models\Route;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentPiece;
use App\Http\Models\ShipmentPiecesRequest;
use App\Http\Models\ShipmentPiecesRequestStatus;
use App\Http\Models\ShipmentsJourney;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Storage;
use App\Http\Models\Admin\ShipmentPieceRequestImage;

class AdminShipmentPieceController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function hold_add_index(){
        return view('admin.shipment_pieces.add');
    }
    public function hold_shipment_details(Request $request){
        $shipment = Shipment::where('tracking_number',$request->tracking_number);
        if($shipment->exists()) {
            $shipment = $shipment->first();
            if($shipment->pieces <= 1){
                return response()->json(['status' => 1, 'error' => 'Shipment doesn\'t have Multiple Pieces!']);
            }

            if($shipment->shipper_status_id != 1){
                return response()->json(['status' => 1, 'error' => 'Shipment not on Booked status anymore!']);
            }

            $details = array();
            $details['id'] = $shipment->id;
            $details['tracking_number'] = $shipment->tracking_number;
            $details['amount'] = $shipment->amount;
            $details['shipper'] = $shipment->user->name;
            $details['origin'] = $shipment->pickup_address->city->name;
            $details['destination'] = $shipment->consignee_city->name;
            $details['pieces'] = $shipment->pieces;
            ShipmentScanningJourneyController::add($shipment->id,22,1,Auth::id(),null,null);
            return  response()->json(['status' => 0, 'details' => $details]);
        }else{
            return response()->json(['status' => 1, 'error' => 'Shipment not found!']);
        }
    }

    public function hold_shipment_submit(Request $request){
        $shipment_ids = explode(',', $request->shipment_ids);
        if(count($shipment_ids) > 0){
            foreach ($shipment_ids as $shipment_id){
                $shipment = Shipment::find($shipment_id);
                if($shipment){
                    $shipment_piece_request = new ShipmentPiecesRequest();
                    $shipment_piece_request->shipment_id = $shipment_id;
                    $shipment_piece_request->added_by = Auth::id();
                    $shipment_piece_request->status = 1;
                    $shipment_piece_request->department_id = session('department_id');
                    $shipment_piece_request->pieces = $shipment->pieces;
                    $shipment_piece_request->save();

                    $shipment->shipper_status_id = 62;
                    $shipment->consignee_status_id = 62;
                    $shipment->save();
                    ShipmentsJourneyController::add($shipment_id, 62, 62, NULL, NULL, NULL, Auth::id());
                    NotificationsController::send(154, $shipment_id);
                }
            }

            return redirect()->back()->with('success', 'Shipments successfully updated!');

        }
        return redirect()->back()->with('error', 'No Shipments Selected!');
    }

    public function hold_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),5);

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
        $request_status = ShipmentPiecesRequestStatus::all();
        return view('admin.shipment_pieces.list')->with(['request_status' => $request_status, 'riders' => $riders, 'routes' => $routes]);
    }

    public function hold_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),65);
        }
        $shipments = ShipmentPiecesRequest::join('shipments', 'shipments.id', '=', 'shipment_pieces_requests.shipment_id')
            ->join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h', 'dc.hub_id', '=', 'h.id')
            ->leftjoin('shipment_pieces_request_statuses as ss', 'ss.id', '=', 'shipment_pieces_requests.request_status_id')
            ->leftjoin('admins','admins.id', '=', 'shipment_pieces_requests.last_updated_by_admin')
            ->leftjoin('users as lub','lub.id', '=', 'shipment_pieces_requests.last_updated_by_user')
            ->select('shipments.id as shId', 'shipments.tracking_number as tracking_number_link', 'shipments.tracking_number', 'shipments.booking_type_id', 'u.name as shipper', 'oc.name as origin', 'dc.name as destination', 'h.name as hub','shipments.amount', 'ss.name as request_status', 'shipment_pieces_requests.created_at','shipment_pieces_requests.last_updated_at','shipment_pieces_requests.last_updated_by_admin', 'shipment_pieces_requests.last_updated_by_user', 'lub.name as updated_by_shipper', 'admins.name as updated_by_admin','shipment_pieces_requests.request_status_id', 'shipment_pieces_requests.status')
        ->where('shipments.shipper_status_id', 62);

        if (session('role_id') != 1) {
            $shipments = $shipments->whereIn('oc.hub_id', session('hubs'));
        }

        $datatables = Datatables::of($shipments)
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
            ->editColumn('status', function ($shipment){
                if($shipment->status == 1){
                    return 'Pending';
                }else{
                    return 'Resolved';
                }
            })
            ->filterColumn('status', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('shipment_pieces_requests.status', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('request_status', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('ss.id', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('last_updated_by', function ($data){
                 if($data->last_updated_by_admin != null){
                     return $data->updated_by_admin . ' (Admin)';
                 }else if($data->last_updated_by_user){
                     return $data->updated_by_shipper . ' (Shipper)';
                 }
            })
            ->addColumn('image_view',function ($data){
                $url = $this->view_attachment($data->shId);
                if($url)
                    return '<a class="btn btn-sm btn-outline-info align-middle" href="'.$url.'" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';
                else
                    return '-';
            })
            ->addColumn("action", function ($result) {
                if (session('role_id') == 1 || in_array(371, session('permissions'))) {
                    $single_piece_button = '<a href="javascript:void(0);" class="dropdown-item single_piece"><i class="ft-plus-circle primary"></i> Switch to Single Piece</a>';
                    $remaining_piece_button = '<a href="javascript:void(0);" class="dropdown-item remaining_piece"><i class="ft-plus-circle primary"></i> Wait for Remaining Piece</a>';
                    $return_button = '<a href="javascript:void(0);" class="dropdown-item return_to_shipper"><i class="ft-plus-circle primary"></i> Return Back to Shipper</a>';
                    $image_upload_button = '<a href="javascript:void(0);" class="dropdown-item image_upload"><i class="ft-plus-circle primary"></i> Image Upload</a>';

                    $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">';
                    $dropdown .= $image_upload_button;
                    if($result->request_status_id == null){
                        $dropdown .= $single_piece_button;
                        $dropdown .= $remaining_piece_button;
                        $dropdown .= $return_button;
                        $dropdown .= '</div>
                      </div>
                    ';
                    }else if(($result->request_status_id == 2) && (Carbon::parse($result->last_updated_at)->diffInDays(Carbon::now()) >= 7)){
                        $dropdown .= $return_button;
                    }else{
                        return '-';
                    }


                    return $dropdown;

                } else {
                    return '';
                }
            });

        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $shipments->whereBetween('shipment_pieces_requests.created_at', [$from,$to]);
        }

        return $datatables->make(true);
    }
    public function single_piece(Request $request){
        $shipment_id = $request->shipment_id;
        if($shipment_id){
            $shipment_piece_request = ShipmentPiecesRequest::where('shipment_id', $shipment_id)->where('status', 1);
            if($shipment_piece_request->exists()){
                $shipment = Shipment::find($shipment_id);
                if($shipment->shipper_status_id == 62){
                    $shipment_piece_request = $shipment_piece_request->first();

                    $shipment->pieces = 1;
                    $shipment->save();

                    ShipmentPiece::where('shipment_id', $shipment_id)->delete();
                    $shipment_piece_request->status = 2;
                    $shipment_piece_request->request_status_id = 1;
                    $shipment_piece_request->last_updated_by_admin = Auth::id();
                    $shipment_piece_request->last_updated_at = Carbon::now();
                    $shipment_piece_request->department_id = session('department_id');
                    $shipment_piece_request->save();

                    return response()->json(['status' => 0,'success' => 'Shipment successfully converted to single!']);
                }
                return response()->json(['status' => 1,'error' => 'Shipment is already modified!']);
            }
            return response()->json(['status' => 1,'error' => 'Shipment request not found!']);
        }
    }
    public function wait_remaining_pieces(Request $request){
        $shipment_id = $request->shipment_id;
        if($shipment_id){
            $shipment_piece_request = ShipmentPiecesRequest::where('shipment_id', $shipment_id)->where('status', 1);
            if($shipment_piece_request->exists()){
                $shipment = Shipment::find($shipment_id);
                if($shipment->shipper_status_id == 62){
                    $shipment_piece_request = $shipment_piece_request->first();
                    $shipment_piece_request->status = 2;
                    $shipment_piece_request->request_status_id = 2;
                    $shipment_piece_request->last_updated_by_admin = Auth::id();
                    $shipment_piece_request->last_updated_at = Carbon::now();
                    $shipment_piece_request->department_id = session('department_id');
                    $shipment_piece_request->save();

                    return response()->json(['status' => 0,'success' => 'Shipment successfully converted to single!']);
                }

            }
            return response()->json(['status' => 1,'error' => 'Shipment request not found!']);
        }
    }
    public function return_back_to_shipper(Request $request){
        $shipment_id = $request->shipment_id;
        if($shipment_id){
            $shipment = Shipment::find($shipment_id);
            if($shipment && ($shipment->shipper_status_id == 62)){
                $shipment_piece_request = ShipmentPiecesRequest::where('shipment_id', $shipment_id);
                if($shipment_piece_request->exists()){
                    $shipment_piece_request = $shipment_piece_request->first();
                    $shipment_piece_request->status = 2;
                    $shipment_piece_request->request_status_id = 3;
                    $shipment_piece_request->last_updated_by_admin = Auth::id();
                    $shipment_piece_request->last_updated_at = Carbon::now();
                    $shipment_piece_request->department_id = session('department_id');
                    $shipment_piece_request->save();

                    return response()->json(['status' => 0,'success' => 'Shipment successfully converted to single!']);
                }
                return response()->json(['status' => 1,'error' => 'Shipment request not found!']);
            }
            return response()->json(['status' => 1,'error' => 'Shipment is not on Multiple Piece Hold Status!']);
        }
    }

    public function return_note_create(Request $request){
        $shipment_ids = $request->shipment_ids;
        $shipment_ids = explode(',', $shipment_ids);
        $invalid_shipment = array();

        foreach ($shipment_ids as $shipment_id){
            $shipment_piece_request = ShipmentPiecesRequest::where('shipment_id', $shipment_id)->first();
            $status = $shipment_piece_request->status;
            $request_status = $shipment_piece_request->request_status_id;
            if($status != 2 && ($request_status != 3 || $request_status != NULL)){
                $shipment = Shipment::where('id', $shipment_id);

                $shipment = $shipment->first();

                $invalid_shipment[] = $shipment->tracking_number;
            }
        }
        if (count($invalid_shipment) > 0) {
            $invalid_shipments = implode(", ", $invalid_shipment);
            return redirect()->back()->with(['error' => "Selected Shipment have different status.<br>" . $invalid_shipments]); 
        }
        $rider = $request->rider_select;
        if(!$rider){
            return redirect()->back()->with('error', 'Rider not selected!');
        }
        if(count($shipment_ids) == 0){
            return redirect()->back()->with('error', 'Shipments not selected!');
        }

        $first_shipment_id = current($shipment_ids);
        $hub_id = Shipment::find($first_shipment_id)->pickup_address->city->hub_id;
        $return_note = new ReturnNote();
        $return_note->rider_id = $rider;
        $return_note->hub_id = $hub_id;
        $return_note->route_id = $request->route;
        $return_note->shipments_count = 0;
        $return_note->admin_id = Auth::id();
        $return_note->save();
        $return_note_id = $return_note->id;
        $shipments_count = 0;
        if($return_note_id){
            foreach ($shipment_ids as $shipment_id){
                $shipment = Shipment::where('id', $shipment_id);

                $shipment = $shipment->first();

                $old_return_note_id = ReturnNoteShipment::where('shipment_id', $shipment_id)->where('status','=', 1)->orderBy('return_note_id', 'desc');

                if ($old_return_note_id->exists()) {
                    $old_return_note_id = $old_return_note_id->first();

                    if(ReturnNote::where('id', $old_return_note_id->return_note_id)->where('status',0)->exists()){
                        $journey = ShipmentsJourney::where('shipment_id',$shipment_id)->latest()->first();

                        ShipmentsJourneyController::add($journey->shipment_id,57,NULL,$journey->status_reason_id,$journey->remarks,NULL, Auth::id(),$journey->reference_1_id,NULL,1,NULL);

                    }
                }

                if(in_array($shipment->booking_type_id, [1,4,5])){
                    ReturnNoteShipment::create(['return_note_id' => $return_note_id, 'shipment_id' => $shipment_id]);
                    $shipment->shipper_status_id = 23;
                    $shipment->consignee_status_id = 23;
                    $shipment->save();
                    ShipmentsJourneyController::add($shipment->id, 23, 23, NULL, NULL, NULL, Auth::id(), $return_note_id, $rider);
                }else
                    if ($shipment->booking_type_id == 2) {//attempt failed and arrived at origin center

                        ReturnNoteShipment::create(['return_note_id' => $return_note_id, 'shipment_id' => $shipment_id]);
                        $shipment->shipper_status_id = 28;
                        $shipment->consignee_status_id = 28;
                        $shipment->save();
                        ShipmentsJourneyController::add($shipment->id, 28, 28, NULL, NULL, NULL, Auth::id(), $return_note_id, $rider);


                    } else if ($shipment->booking_type_id == 3) {//attempt failed and arrived at origin center

                        ReturnNoteShipment::create(['return_note_id' => $return_note_id, 'shipment_id' => $shipment_id]);
                        $shipment->shipper_status_id = 34;
                        $shipment->consignee_status_id = 34;
                        $shipment->save();
                        ShipmentsJourneyController::add($shipment->id, 34, 34, NULL, NULL, NULL, Auth::id(), $return_note_id, $rider);


                    }else{
                        ReturnNoteShipment::create(['return_note_id' => $return_note_id, 'shipment_id' => $shipment_id]);
                        $shipment->shipper_status_id = 23;
                        $shipment->consignee_status_id = 23;
                        $shipment->save();
                        ShipmentsJourneyController::add($shipment->id, 23, 23, NULL, NULL, NULL, Auth::id(), $return_note_id, $rider);
                    }
                $shipments_count++;
            }
            $return_note->shipments_count = $shipments_count;
            $return_note->save();
            return redirect()->back()->with(['success' => "Return note has been created with Return Note Number:" . $return_note_id,'print'=>$return_note_id]);
        }

    }
    public function hold_resolved_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),295);
        $request_status = ShipmentPiecesRequestStatus::all();
        return view('admin.shipment_pieces.resolved_index')->with(['request_status' => $request_status]);
    }
    public function hold_resolved_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),296);
        }
        $shipments = ShipmentPiecesRequest::join('shipments', 'shipments.id', '=', 'shipment_pieces_requests.shipment_id')
            ->join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h', 'dc.hub_id', '=', 'h.id')
            ->leftjoin('shipment_pieces_request_statuses as ss', 'ss.id', '=', 'shipment_pieces_requests.request_status_id')
            ->leftjoin('admins','admins.id', '=', 'shipment_pieces_requests.last_updated_by_admin')
            ->leftjoin('users as lub','lub.id', '=', 'shipment_pieces_requests.last_updated_by_user')
            ->select('shipments.id as shId', 'shipments.tracking_number as tracking_number_link', 'shipments.tracking_number', 'shipments.booking_type_id', 'u.name as shipper', 'oc.name as origin', 'dc.name as destination', 'h.name as hub','shipments.amount', 'ss.name as request_status', 'shipment_pieces_requests.created_at','shipment_pieces_requests.last_updated_at','shipment_pieces_requests.last_updated_by_admin', 'shipment_pieces_requests.last_updated_by_user', 'lub.name as updated_by_shipper', 'admins.name as updated_by_admin','shipment_pieces_requests.created_at','shipment_pieces_requests.request_status_id', 'shipment_pieces_requests.status','shipment_pieces_requests.pieces')
            ->where('shipment_pieces_requests.status', 2);

        if (session('role_id') != 1) {
            $shipments = $shipments->whereIn('oc.hub_id', session('hubs'));
        }

        $datatables = Datatables::of($shipments)
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
            ->editColumn('status', function ($shipment){
                if($shipment->status == 1){
                    return 'Pending';
                }else{
                    return 'Resolved';
                }
            })
            ->filterColumn('status', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('shipment_pieces_requests.status', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('request_status', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('ss.id', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('last_updated_by', function ($data){
                if($data->last_updated_by_admin != null){
                    return $data->updated_by_admin;
                }else if($data->last_updated_by_user){
                    return $data->updated_by_shipper;
                }
            });

        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $shipments->whereBetween('shipment_pieces_requests.created_at', [$from,$to]);
        }

        return $datatables->make(true);
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
                      td.complaint {
                            background: #09262e !important;
                            color: #ffffff;
                       }
                       div.page
                        {
                            page-break-after: always;
                            page-break-inside: avoid;
                        }
                    </style>
                  </head>
                  <body>
                    <div>
      ';
        $return_note = ReturnNote::where('id',$request->id);
        if($return_note->exists()) {
            $total_shipments = 0;
            $total_users = 0;
            $try_and_buy_total_users = 0;
            $replacement_total_users = 0;
            $try_and_buy_total_shipments = 0;
            $shipment_ids = ReturnNoteShipment::where('return_note_id', $request->id)->select('shipment_id')->get();
            $filtered_shipments = Shipment::whereIn('id', $shipment_ids)->orderBy('id')->get();
            $filtered_shipments_regular = Shipment::whereIn('id', $shipment_ids)->where('booking_type_id', '=', '1')->orderBy('id')->get();
            $filtered_shipments_replacement = Shipment::whereIn('id', $shipment_ids)->where('booking_type_id', '=', '2')->orderBy('id')->get();
            $filtered_shipments_try_and_buy = Shipment::whereIn('id', $shipment_ids)->where('booking_type_id', '=', '3')->orderBy('id')->get();
            $filtered_shipments_users = Shipment::whereIn('id', $shipment_ids)->orderBy('id')->groupBy('user_id')->get();
            $shipment_details = '';

            $shipment_details .= '<div class="page text-center">';

            if(count($filtered_shipments_regular) > 0){
                $shipment_details .= '
                              <table class="table table-sm table-bordered border mt-1">
                                <tbody>
                                    <tr>
                                        <td class="color primary" colspan="7"><strong style="font-size: large">SUMMARY - Regular</strong></td>
                                    </tr>
                                  <tr>
                                    <td class="color primary"><strong>S. No.</strong></td>
                                    <td class="color primary"><strong>Client Name & Phone No(s).</strong></td>
                                    <td class="color primary"><strong>Contact Person</strong></td>
                                    <td class="color primary"><strong>Contact Person Phone</strong></td>
                                    <td class="color primary"><strong>Client Address</strong></td>
                                    <td class="color primary"><strong>Total Shipments</strong></td>
                                    <td class="color primary"><strong>Sign</strong></td>
                                  </tr>
                ';

                foreach ($filtered_shipments_users as $filtered_shipments_user) {
                    $user_shipment_collection_charges[$filtered_shipments_user->user_id] = 0;
                    $user_total_shipments[$filtered_shipments_user->user_id] = 0;
                    foreach ($filtered_shipments_regular as $shipment) {
                        if ($shipment->user_id == $filtered_shipments_user->user_id) {
                            $user_total_shipments[$filtered_shipments_user->user_id]++;
                        }
                    }
                    if ($user_total_shipments[$filtered_shipments_user->user_id] > 0) {
                        $total_users++;
                        $shipment_details_row_start_summary = '
                                  <tr>
                                    <td>' . $total_users . '</td>
                                    <td>' . $filtered_shipments_user->user->name . ' | ' . $filtered_shipments_user->user->phone . (($filtered_shipments_user->user->phone2) ? (' / ' . $filtered_shipments_user->user->phone2) : '') . '</td>
                                    <td>' . $filtered_shipments_user->pickup_address->poc . '</td>
                                    <td>' . $filtered_shipments_user->pickup_address->phone . '</td>
                                    <td>' . $filtered_shipments_user->pickup_address->pickup_address . '</td>
                                    <td>' . $user_total_shipments[$filtered_shipments_user->user_id] . '</td>
                                    <td></td>
                        ';

                        $shipment_details .= $shipment_details_row_start_summary;
                    }
                }
                $shipment_details .= '
                            </tbody>
                          </table>
                         
                ';
            }
            if(count($filtered_shipments_replacement) > 0){

                $shipment_details .= '
                          <table class="table table-sm table-bordered border mt-1">
                            <tbody>
                                <tr>
                                    <td class="color primary" colspan="7"><strong style="font-size: large">SUMMARY - Replacement</strong></td>
                                </tr>
                              <tr>
                                <td class="color primary"><strong>S. No.</strong></td>
                                <td class="color primary"><strong>Client Name & Phone No(s).</strong></td>
                                <td class="color primary"><strong>Contact Person</strong></td>
                                <td class="color primary"><strong>Contact Person Phone</strong></td>
                                <td class="color primary"><strong>Client Address</strong></td>
                                <td class="color primary"><strong>Total Shipments</strong></td>
                                <td class="color primary"><strong>Sign</strong></td>
                              </tr>
            ';

                foreach ($filtered_shipments_users as $filtered_shipments_user){
                    $user_shipment_collection_charges[$filtered_shipments_user->user_id] = 0;
                    $user_total_shipments[$filtered_shipments_user->user_id] = 0;
                    foreach ($filtered_shipments_replacement as $shipment) {
                        if($shipment->user_id == $filtered_shipments_user->user_id){
                            $user_total_shipments[$filtered_shipments_user->user_id]++;
                        }
                    }
                    if($user_total_shipments[$filtered_shipments_user->user_id] > 0){
                        $replacement_total_users++;
                        $shipment_details_row_start_summary = '
                              <tr>
                                <td>' . $replacement_total_users . '</td>
                                <td>' . $filtered_shipments_user->user->name . ' | ' . $filtered_shipments_user->user->phone . (($filtered_shipments_user->user->phone2) ? (' / ' . $filtered_shipments_user->user->phone2) : '') . '</td>
                                <td>' . $filtered_shipments_user->pickup_address->poc . '</td>
                                <td>' . $filtered_shipments_user->pickup_address->phone . '</td>
                                <td>' . $filtered_shipments_user->pickup_address->pickup_address . '</td>
                                <td>' . $user_total_shipments[$filtered_shipments_user->user_id] . '</td>
                                <td></td>
                    ';

                        $shipment_details .= $shipment_details_row_start_summary;
                    }
                }
                $shipment_details .= '
                        </tbody>
                      </table>
                     
        ';
            }
            if(count($filtered_shipments_try_and_buy) > 0){

                $shipment_details .= '
                          <table class="table table-sm table-bordered border mt-1">
                            <tbody>
                                <tr>
                                    <td class="color primary" colspan="7"><strong style="font-size: large">SUMMARY - Try & Buy</strong></td>
                                </tr>
                              <tr>
                                <td class="color primary"><strong>S. No.</strong></td>
                                <td class="color primary"><strong>Client Name & Phone No(s).</strong></td>
                                <td class="color primary"><strong>Contact Person</strong></td>
                                <td class="color primary"><strong>Contact Person Phone</strong></td>
                                <td class="color primary"><strong>Client Address</strong></td>
                                <td class="color primary"><strong>Total Shipments</strong></td>
                                <td class="color primary"><strong>Sign</strong></td>
                              </tr>
            ';

                foreach ($filtered_shipments_users as $filtered_shipments_user){
                    $user_shipment_collection_charges[$filtered_shipments_user->user_id] = 0;
                    $user_total_shipments[$filtered_shipments_user->user_id] = 0;
                    foreach ($filtered_shipments_try_and_buy as $shipment) {
                        if($shipment->user_id == $filtered_shipments_user->user_id){
                            $user_total_shipments[$filtered_shipments_user->user_id]++;
                        }
                    }
                    if($user_total_shipments[$filtered_shipments_user->user_id] > 0){
                        $try_and_buy_total_users++;
                        $shipment_details_row_start_summary = '
                              <tr>
                                <td>' . $try_and_buy_total_users . '</td>
                                <td>' . $filtered_shipments_user->user->name . ' | ' . $filtered_shipments_user->user->phone . (($filtered_shipments_user->user->phone2) ? (' / ' . $filtered_shipments_user->user->phone2) : '') . '</td>
                                <td>' . $filtered_shipments_user->pickup_address->poc . '</td>
                                <td>' . $filtered_shipments_user->pickup_address->phone . '</td>
                                <td>' . $filtered_shipments_user->pickup_address->pickup_address . '</td>
                                <td>' . $user_total_shipments[$filtered_shipments_user->user_id] . '</td>
                                <td></td>
                    ';

                        $shipment_details .= $shipment_details_row_start_summary;
                    }
                }
                $shipment_details .= '
                        </tbody>
                      </table>
                     
        ';
            }
            $shipment_details .= '
                      </div>
                     
        ';
            foreach ($filtered_shipments_users as $filtered_shipments_user){
                $shipment_details .= '<div class="mb-1 text-center">';

                $shipment_details .= '
                          <table class="table table-sm table-bordered border">
                            <tbody>
                                <tr>
                                    <td class="color primary" colspan="9"><strong style="font-size: large">' . $filtered_shipments_user->user->name . '</strong></td>
                                </tr>
                              <tr>
                                <td class="color primary"><strong>S. No.</strong></td>
                                <td class="color primary"><strong>Tracking No.</strong></td>
                                <td class="color primary"><strong>Client Name & Phone No(s).</strong></td>
                                <td class="color primary"><strong>Contact Person</strong></td>
                                <td class="color primary"><strong>Contact Person Phone</strong></td>
                                <td class="color primary"><strong>Client Address</strong></td>
                                <td class="color primary"><strong>No. of Items</strong></td>
                                <td class="color primary"><strong>Collection Charges</strong></td>
                                <td class="color primary" style="width:200px;"><strong>Sign</strong></td>
                              </tr>
            ';

                foreach ($filtered_shipments as $shipment) {
                    if($shipment->user_id == $filtered_shipments_user->user_id){
                        $total_shipments++;
                        $class = null;
                        if(CrmRequest::where('shipment_id',$shipment->id)->where('case_nature_id',1)->whereIn('status_id',[2, 3, 5])->exists()){
                            $class = 'complaint';
                        }
                        $shipment_details_row_start = '
                              <tr>
                                <td>' . $total_shipments . '</td>
                                <td class="'. $class .'">' . $shipment->tracking_number . '</td>
                                <td>' . $shipment->user->name . ' | ' . $shipment->user->phone . (($shipment->user->phone2) ? (' / ' . $shipment->user->phone2) : '') . '</td>
                                <td>' . $shipment->pickup_address->poc . '</td>
                                <td>' . $shipment->pickup_address->phone . '</td>
                                <td>' . $shipment->pickup_address->pickup_address . '</td>
                                <td>' . $shipment->items->where('bought', 0)->sum('quantity') . '</td>
                    ';

                        if ($shipment->booking_type_id != 4) {
                            $shipment_details_row_start .= '
                                <td></td>
                        ';
                        }
                        else {
                            if ($shipment->charges_mode_id == 1) {
                                $shipment_details_row_start .= '
                                <td>' . number_format($shipment->return_charges) . '</td>
                            ';
                            }
                            else {
                                $shipment_details_row_start .= '
                                <td>' . number_format($shipment->amount) . '</td>
                            ';
                            }
                        }

                        $shipment_details_row_start .= '
                                <td></td>
    
                              </tr>
                ';

                        $shipment_details .= $shipment_details_row_start;
                    }
                }
                $shipment_details .= '
                        </tbody>
                      </table>
                      </div>
        ';
            }
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
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>
                            <td class="text-center align-middle color primary"><strong>Return Note (Multiple Piece Return)</strong></td>
                            <td class="text-center align-middle color secondary">Created at ' . $return_note_details->created_at . '</br> by ' . ucfirst($return_note_details->admin->name) . '</td>
                            <td class="text-center align-middle color secondary">Printed at ' . Carbon::now() . '</br> by ' . ucfirst(Auth::user()->name) . '</td>
                          </tr>
                         
                          <tr>
                            <td class="color secondary"><strong>Rider Name</strong></td>
                            <td>' . $rider_name . '</td>
                            <td colspan="2" rowspan="7" class="pl-1 pr-1 text-center align-middle">
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
    public function upload_attachment(Request $request){
        $shipment_id = $request->shipment_image_id;
        if ($request->hasFile('upload_attachment')) {
            $filename = 'shipment_piece_' . $shipment_id . '.png';

            $file = $request->file('upload_attachment');

            Storage::disk('public')->putFileAs('shipment_pieces\attachment', $file, $filename);
            if(ShipmentPieceRequestImage::where('shipment_id','=',$shipment_id)->exists()) {
                $image = ShipmentPieceRequestImage::where('shipment_id','=',$shipment_id)->first();
                $image->image = $filename;
                $image->shipment_id = $shipment_id;
                $image->updated_by = Auth::id();
                $image->save();
            }
            else{
                $image = new ShipmentPieceRequestImage();
                $image->image = $filename;
                $image->shipment_id = $shipment_id;
                $image->created_by = Auth::id();
                $image->save();
            }
            return redirect()->back()->with('success', 'Image Uploaded Successfully');
        }
        else{
            return redirect()->back()->with('error', 'Image not Uploaded!');
        }
    }
    public function view_attachment($id){
         $image = ShipmentPieceRequestImage::where('shipment_id','=',$id)->first();
         if($image){
            $file = $image->image;
            $url = Storage::url('shipment_pieces/attachment/'. $file);
            return $url;
         }else{
           return '';  
         }
    }
    public function single_piece_bulk(Request $request){
        
        $shipment_ids = $request->shipment_ids;
        $invalid_shipment = array();

        foreach ($shipment_ids as $shipment_id){
            $shipment_piece_request = ShipmentPiecesRequest::where('shipment_id', $shipment_id)->first();
            $status = $shipment_piece_request->status;
            $request_status = $shipment_piece_request->request_status_id;
            if($status == 2 && $request_status == 3){
                $shipment = Shipment::where('id', $shipment_id);

                $shipment = $shipment->first();

                $invalid_shipment[] = $shipment->tracking_number;
            }
        }
        if (count($invalid_shipment) > 0) {
            $invalid_shipments = implode(", ", $invalid_shipment);
            return response()->json(['status' => 1,'error' => 'Selected Shipments had Already been Resolved.<br>' .$invalid_shipments ]); 
        }
        foreach ($shipment_ids as $shipment_id){
            if($shipment_id){
                $shipment_piece_request = ShipmentPiecesRequest::where('shipment_id', $shipment_id)->where('status', 1);
                if($shipment_piece_request->exists()){
                    $shipment = Shipment::find($shipment_id);
                    if($shipment->shipper_status_id == 62){
                        $shipment_piece_request = $shipment_piece_request->first();

                        $shipment->pieces = 1;
                        $shipment->save();

                        ShipmentPiece::where('shipment_id', $shipment_id)->delete();
                        $shipment_piece_request->status = 2;
                        $shipment_piece_request->request_status_id = 1;
                        $shipment_piece_request->last_updated_by_admin = Auth::id();
                        $shipment_piece_request->last_updated_at = Carbon::now();
                        $shipment_piece_request->department_id = session('department_id');
                        $shipment_piece_request->save();

                        // return response()->json(['status' => 0,'success' => 'Shipment successfully converted to single!']);
                    }
                    // return response()->json(['status' => 1,'error' => 'Shipment is already modified!']);
                }
                // return response()->json(['status' => 1,'error' => 'Shipment request not found!']);
            }
        }
        return response()->json(['status' => 0,'success' => 'Shipment successfully converted to single!']);
    }
    public function wait_remaining_pieces_bulk(Request $request){
        $shipment_ids = $request->shipment_ids;
        $invalid_shipment = array();

        foreach ($shipment_ids as $shipment_id){
            $shipment_piece_request = ShipmentPiecesRequest::where('shipment_id', $shipment_id)->first();
            $status = $shipment_piece_request->status;
            $request_status = $shipment_piece_request->request_status_id;
            if($status == 2 && $request_status == 3){
                $shipment = Shipment::where('id', $shipment_id);

                $shipment = $shipment->first();

                $invalid_shipment[] = $shipment->tracking_number;
            }
        }
        if (count($invalid_shipment) > 0) {
            $invalid_shipments = implode(", ", $invalid_shipment);
            return response()->json(['status' => 1,'error' => 'Selected Shipments had Already been Resolved.<br>'. $invalid_shipments ]); 
        }
        foreach ($shipment_ids as $shipment_id){
            if($shipment_id){
                $shipment_piece_request = ShipmentPiecesRequest::where('shipment_id', $shipment_id)->where('status', 1);
                if($shipment_piece_request->exists()){
                    $shipment = Shipment::find($shipment_id);
                    if($shipment->shipper_status_id == 62){
                        $shipment_piece_request = $shipment_piece_request->first();
                        $shipment_piece_request->status = 2;
                        $shipment_piece_request->request_status_id = 2;
                        $shipment_piece_request->last_updated_by_admin = Auth::id();
                        $shipment_piece_request->last_updated_at = Carbon::now();
                        $shipment_piece_request->department_id = session('department_id');
                        $shipment_piece_request->save();

                        // return response()->json(['status' => 0,'success' => 'Shipment successfully converted to single!']);
                    }
                }
                // return response()->json(['status' => 1,'error' => 'Shipment request not found!']);
            }
        }
        return response()->json(['status' => 0,'success' => 'Shipment successfully converted to wait for remaining!']);
    }
    public function return_back_to_shipper_bulk(Request $request){
        $shipment_ids = $request->shipment_ids;
        $invalid_shipment = array();

        foreach ($shipment_ids as $shipment_id){
            $shipment_piece_request = ShipmentPiecesRequest::where('shipment_id', $shipment_id)->first();
            $status = $shipment_piece_request->status;
            $request_status = $shipment_piece_request->request_status_id;
            if($status == 2 && $request_status == 3){
                $shipment = Shipment::where('id', $shipment_id);

                $shipment = $shipment->first();

                $invalid_shipment[] = $shipment->tracking_number;
            }
        }
        if (count($invalid_shipment) > 0) {
            $invalid_shipments = implode(", ", $invalid_shipment);
            return response()->json(['status' => 1,'error' => 'Selected Shipments had Already been Resolved.<br>'. $invalid_shipments ]); 
        }
        foreach ($shipment_ids as $shipment_id){
            if($shipment_id){
                $shipment = Shipment::find($shipment_id);
                if($shipment && ($shipment->shipper_status_id == 62)){
                    $shipment_piece_request = ShipmentPiecesRequest::where('shipment_id', $shipment_id);
                    if($shipment_piece_request->exists()){
                        $shipment_piece_request = $shipment_piece_request->first();
                        $shipment_piece_request->status = 2;
                        $shipment_piece_request->request_status_id = 3;
                        $shipment_piece_request->last_updated_by_admin = Auth::id();
                        $shipment_piece_request->last_updated_at = Carbon::now();
                        $shipment_piece_request->department_id = session('department_id');
                        $shipment_piece_request->save();

                        // return response()->json(['status' => 0,'success' => 'Shipment successfully converted to single!']);
                    }
                    // return response()->json(['status' => 1,'error' => 'Shipment request not found!']);
                }
                // return response()->json(['status' => 1,'error' => 'Shipment is not on Multiple Piece Hold Status!']);
            }
        }
        return response()->json(['status' => 0,'success' => 'Shipment successfully converted to return to shipper!']);
    }
}
