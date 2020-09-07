<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentPiece;
use App\Http\Models\ShipmentPiecesRequest;
use App\Http\Models\ShipmentPiecesRequestStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

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
                    $shipment_piece_request->save();

                    $shipment->shipper_status_id = 62;
                    $shipment->consignee_status_id = 62;
                    $shipment->save();
                    ShipmentsJourneyController::add($shipment_id, 62, 62, NULL, NULL, NULL, Auth::id());
                }
            }

            return redirect()->back()->with('success', 'Shipments successfully updated!');

        }
        return redirect()->back()->with('error', 'No Shipments Selected!');
    }

    public function hold_index(){
        $request_status = ShipmentPiecesRequestStatus::all();
        return view('admin.shipment_pieces.list')->with(['request_status' => $request_status]);
    }

    public function hold_list(Request $request){
        $shipments = ShipmentPiecesRequest::join('shipments', 'shipments.id', '=', 'shipment_pieces_requests.shipment_id')
            ->join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h', 'dc.hub_id', '=', 'h.id')
            ->leftjoin('shipment_pieces_request_statuses as ss', 'ss.id', '=', 'shipment_pieces_requests.request_status_id')
            ->leftjoin('admins','admins.id', '=', 'shipment_pieces_requests.last_updated_by_admin')
            ->leftjoin('users as lub','lub.id', '=', 'shipment_pieces_requests.last_updated_by_user')
            ->select('shipments.id as shId', 'shipments.tracking_number as tracking_number_link', 'shipments.tracking_number', 'shipments.booking_type_id', 'u.name as shipper', 'oc.name as origin', 'dc.name as destination', 'h.name as hub','shipments.amount', 'ss.name as request_status', 'shipment_pieces_requests.created_at','shipment_pieces_requests.last_updated_at','shipment_pieces_requests.last_updated_by_admin', 'shipment_pieces_requests.last_updated_by_user', 'lub.name as updated_by_shipper', 'admins.name as updated_by_admin','shipment_pieces_requests.created_at','shipment_pieces_requests.request_status_id', 'shipment_pieces_requests.status')
        ->where('shipments.shipper_status_id', 62);

        if (session('role_id') != 1) {
            $shipments = $shipments->whereIn('dc.hub_id', session('hubs'));
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
            })
            ->addColumn("action", function ($result) {
                if (session('role_id') == 1 || in_array(34, session('permissions'))) {
                    $single_piece_button = '<a href="javascript:void(0);" class="dropdown-item single_piece"><i class="ft-plus-circle primary"></i> Switch to Single Piece</a>';
                    $remaining_piece_button = '<a href="javascript:void(0);" class="dropdown-item remaining_piece"><i class="ft-plus-circle primary"></i> Wait for Remaining Piece</a>';
                    $return_button = '<a href="javascript:void(0);" class="dropdown-item return_to_shipper"><i class="ft-plus-circle primary"></i> Return Back to Shipper</a>';

                    $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">';
                    if($result->request_status_id == null){
                        $dropdown .= $single_piece_button;
                        $dropdown .= $remaining_piece_button;
                        $dropdown .= $return_button;
                        $dropdown .= '</div>
                      </div>
                    ';
                    }else{
                        $dropdown = '';
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
            $shipment_piece_request = ShipmentPiecesRequest::where('shipment_id', $shipment_id);
            if($shipment_piece_request->exists()){
                $shipment_piece_request = $shipment_piece_request->first();
                $shipment = Shipment::find($shipment_id);
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
            return response()->json(['status' => 1,'error' => 'Shipment request not found!']);
        }
    }
    public function wait_remaining_pieces(Request $request){
        $shipment_id = $request->shipment_id;
        if($shipment_id){
            $shipment_piece_request = ShipmentPiecesRequest::where('shipment_id', $shipment_id);
            if($shipment_piece_request->exists()){
                $shipment_piece_request = $shipment_piece_request->first();
                $shipment_piece_request->status = 2;
                $shipment_piece_request->request_status_id = 2;
                $shipment_piece_request->last_updated_by_admin = Auth::id();
                $shipment_piece_request->last_updated_at = Carbon::now();
                $shipment_piece_request->department_id = session('department_id');
                $shipment_piece_request->save();

                return response()->json(['status' => 0,'success' => 'Shipment successfully converted to single!']);
            }
            return response()->json(['status' => 1,'error' => 'Shipment request not found!']);
        }
    }
    //in progress
    public function return_back_to_shipper(Request $request){
        $shipment_id = $request->shipment_id;
        if($shipment_id){
            $shipment_piece_request = ShipmentPiecesRequest::where('shipment_id', $shipment_id);
            if($shipment_piece_request->exists()){
                $shipment_piece_request = $shipment_piece_request->first();
                $shipment_piece_request->status = 2;
                $shipment_piece_request->request_status_id = 2;
                $shipment_piece_request->last_updated_by_admin = Auth::id();
                $shipment_piece_request->last_updated_at = Carbon::now();
                $shipment_piece_request->department_id = session('department_id');
                $shipment_piece_request->save();

                return response()->json(['status' => 0,'success' => 'Shipment successfully converted to single!']);
            }
            return response()->json(['status' => 1,'error' => 'Shipment request not found!']);
        }
    }
}
