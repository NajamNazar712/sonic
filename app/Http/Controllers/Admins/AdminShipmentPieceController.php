<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Shipment;
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
        return view('admin.shipment_pieces.list');
    }

    public function hold_list(Request $request){
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h', 'dc.hub_id', '=', 'h.id')
            ->join('shipment_status as ss', 'ss.id', '=', 'shipments.shipper_status_id')
            ->leftJoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where('shipments_journey.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 62)'));
            })
            ->select('shipments.id as shId', 'shipments.tracking_number as tracking_number_link', 'shipments.tracking_number', 'u.name as shipper', 'oc.name as origin', 'dc.name as destination', 'h.name as hub','shipments.amount', 'ss.name as status', 'sj.created_at as current_status_date')
            ->where('shipments.shipper_status_id', 62);

        if (session('role_id') != 1) {
            $shipments = $shipments->whereIn('dc.hub_id', session('hubs'));
        }

        $datatables = Datatables::of($shipments)
            ->setRowAttr([
                'class' => function ($shipments) {
                    if ($shipments->complaint != null) {
                        return 'complaint_row';
                    }else if($shipments->booking_type_id == 3){
                        return "tnb_row";
                    } else {
                        return '';
                    }
                },
            ])
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
            ->orderColumn('u.name', 'u.name $1, usi.poc $1')
            ->editColumn('arrival', function ($shipments) {
                if ($shipments->arrival) {
                    return $shipments->arrival;
                } else {
                    return " - ";
                }
            })
            ->filterColumn('status', function ($query, $keyword) {

                if ($keyword != '') {
                    $query->where('ss.id', $keyword);
                } else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn("action", function ($result) {
                if (session('role_id') == 1 || in_array(34, session('permissions'))) {
                    $dropdown = '
                      <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                        <div class="dropdown-menu dropdown-menu-sm">
                            <a href="#" class="dropdown-item dispute_modal"><i class="ft-alert-circle primary"></i> Dispute</a>
                        </div>
                      </div>
                    ';

                    return $dropdown;

                } else {
                    return '';
                }
            });

        return $datatables->make(true);
    }
}
