<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Models\Shipment;
use App\Http\Models\ShipmentPiece;
use App\Http\Models\ShipmentPiecesRequest;
use App\Http\Models\ShipmentPiecesRequestStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;

class ShipperShipmentPieceController extends Controller
{
    public function __construct() {
        $this->middleware('auth:web,substitute_users');

        $this->middleware('Permission');
    }

    public function hold_index(){
        $request_status = ShipmentPiecesRequestStatus::all();
        return view('client.shipment_piece.hold_index')->with(['request_status' => $request_status]);
    }
    public function hold_list(Request $request){
        $shipments = ShipmentPiecesRequest::join('shipments', 'shipments.id', '=', 'shipment_pieces_requests.shipment_id')
            ->join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->leftjoin('shipment_pieces_request_statuses as ss', 'ss.id', '=', 'shipment_pieces_requests.request_status_id')
            ->select('shipments.id as shId', 'shipments.tracking_number as tracking_number_link', 'shipments.tracking_number', 'shipments.booking_type_id', 'u.name as shipper', 'oc.name as origin', 'dc.name as destination','shipments.amount', 'ss.name as request_status', 'shipment_pieces_requests.created_at', 'shipment_pieces_requests.request_status_id', 'shipment_pieces_requests.status')
            ->where('shipments.shipper_status_id', 62)->where('shipment_pieces_requests.status', 1);

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
            ->filterColumn('request_status', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('ss.id', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn("action", function ($result) {

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
                        return '-';
                    }
                    return $dropdown;
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
            $shipment_piece_request = ShipmentPiecesRequest::where('shipment_id', $shipment_id)->where('status', 1);;
            if($shipment_piece_request->exists()){
                $shipment = Shipment::find($shipment_id);
                if($shipment->shipper_status_id == 62){
                    $shipment_piece_request = $shipment_piece_request->first();

                    $shipment->pieces = 1;
                    $shipment->save();

                    ShipmentPiece::where('shipment_id', $shipment_id)->delete();
                    $shipment_piece_request->status = 2;
                    $shipment_piece_request->request_status_id = 1;
                    $shipment_piece_request->last_updated_by_user = Auth::id();
                    $shipment_piece_request->last_updated_at = Carbon::now();
                    $shipment_piece_request->department_id = session('department_id');
                    $shipment_piece_request->save();

                    return response()->json(['status' => 0,'success' => 'Shipment successfully converted to single!']);
                }
                return response()->json(['status' => 1,'error' => 'Shipment is already modified!']);
            }
            return response()->json(['status' => 1,'error' => 'Shipment request not found or already modified!']);
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
                    $shipment_piece_request->last_updated_by_user = Auth::id();
                    $shipment_piece_request->last_updated_at = Carbon::now();
                    $shipment_piece_request->save();

                    return response()->json(['status' => 0,'success' => 'Shipment successfully converted to single!']);
                }
                return response()->json(['status' => 1,'error' => 'Shipment is already modified!']);
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
                    $shipment = Shipment::find($shipment_id);
                    if($shipment->shipper_status_id == 62){
                        $shipment_piece_request = $shipment_piece_request->first();
                        $shipment_piece_request->status = 2;
                        $shipment_piece_request->request_status_id = 3;
                        $shipment_piece_request->last_updated_by_user = Auth::id();
                        $shipment_piece_request->last_updated_at = Carbon::now();
                        $shipment_piece_request->save();

                        return response()->json(['status' => 0,'success' => 'Shipment successfully converted to single!']);
                    }
                    return response()->json(['status' => 1,'error' => 'Shipment is already modified!']);

                }
                return response()->json(['status' => 1,'error' => 'Shipment request not found!']);
            }
            return response()->json(['status' => 1,'error' => 'Shipment is not on Multiple Piece Hold Status!']);
        }
    }

    public function resolved_index(){
        $request_status = ShipmentPiecesRequestStatus::all();
        return view('client.shipment_piece.resolved_index')->with(['request_status' => $request_status]);
    }
    public function resolved_list(Request $request){
        $shipments = ShipmentPiecesRequest::join('shipments', 'shipments.id', '=', 'shipment_pieces_requests.shipment_id')
            ->join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->leftjoin('shipment_pieces_request_statuses as ss', 'ss.id', '=', 'shipment_pieces_requests.request_status_id')
            ->select('shipments.id as shId', 'shipments.tracking_number as tracking_number_link', 'shipments.tracking_number', 'shipments.booking_type_id', 'u.name as shipper', 'oc.name as origin', 'dc.name as destination','shipments.amount', 'ss.name as request_status', 'shipment_pieces_requests.created_at', 'shipment_pieces_requests.request_status_id', 'shipment_pieces_requests.status')
            ->where('shipments.shipper_status_id', 62)->where('shipment_pieces_requests.status', 2);

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
            ->filterColumn('request_status', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('ss.id', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            });

        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $shipments->whereBetween('shipment_pieces_requests.created_at', [$from,$to]);
        }

        return $datatables->make(true);
    }
}
