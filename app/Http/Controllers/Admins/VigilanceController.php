<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Admin\Vigilance\VigilanceVerification;
use App\Http\Models\Admin\Vigilance\VigilanceVerifiedShipment;
use App\Http\Models\Rider;
use App\Http\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;
use DB;
class VigilanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function verification_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(), 562);
        $riders = Rider::where('status', 1)->select('id', 'name')->get();
        return view('admin.vigilance.verification')->with(['riders' => $riders]);
    }

    public function verification_list(Request $request){
        $delivery_note = DeliveryNote::join('delivery_note_shipments as dns', 'dns.delivery_note_id', '=', 'delivery_notes.id')
            ->join('riders as r', 'r.id', '=', 'delivery_notes.rider_id')
            ->join('shipments as s', 's.id', '=', 'dns.shipment_id')
            ->join('users as u', 'u.id', '=', 's.user_id')
            ->join('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 's.id')
                    ->where('shipments_journey.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = s.id and shipments_journey.shipper_status_id = 5)'));
            })
            ->join('shipment_status as ss', 'ss.id', '=', 's.shipper_status_id')
            ->select('s.tracking_number', 'delivery_notes.id as delivery_note_id', 'ss.name as shipment_status', 'shipments_journey.created_at as status_date', 'r.name as rider', 'u.name as shipper', 's.amount')
        ->where('delivery_notes.status', 0)
        ->whereDate('delivery_notes.created_at', Carbon::today());


        if ($delivery_note_id = $request->get('search_delivery_note_id')) {

            $delivery_note->where('delivery_notes.id', '=', $delivery_note_id);
        }

        if ($rider_id = $request->get('search_rider')) {

            $delivery_note->where('delivery_notes.rider_id', '=', $rider_id);
        }

        $datatables = Datatables::of($delivery_note)
            ->addColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->addColumn('delivery_note', function ($deliveries) {
                return str_pad($deliveries->delivery_note_id, 6, '0', STR_PAD_LEFT);
            });

        return $datatables->make(true);
    }

    public function verification_info(Request $request){
        $delivery_note_id = $request->delivery_note_id;
        if($delivery_note_id){
            $delivery_note = DeliveryNote::where('status', 0)->where('id', $delivery_note_id);
            if($delivery_note->exists()){
                $tracking_number = $request->tracking_number;
                $shipment = Shipment::where('tracking_number', $tracking_number);
                if($shipment->exists()){
                    $shipment = $shipment->first();
                    $data = array();
                    $data['id'] = $shipment->id;
                    $data['tracking_number'] = $shipment->tracking_number;
                    $data['origin'] = $shipment->pickup_address->city->name;
                    $data['destination'] = $shipment->consignee_city->name;
                    $data['amount'] = $shipment->amount;
                    $delivery_note_shipment = DeliveryNoteShipment::where('delivery_note_id', $delivery_note_id)->where('shipment_id', $shipment->id);
                    if($delivery_note_shipment->exists()){
                        $data['verify'] = 'Verify';
                        $data['verify_id'] = 1;
                    }
                    else{
                        $data['verify'] = 'Excess';
                        $data['verify_id'] = 2;
                    }

                    return response()->json(['status' => 1, 'details' => $data]);

                }
                return response()->json(['status' => 0, 'error' => 'Invalid tracking number!']);
            }
            else{
                return response()->json(['status' => 0, 'error' => 'Delivery note not found/ already completed!']);
            }
        }
        return response()->json(['status' => 0, 'error' => 'Delivery note not found!']);
    }

    public function verification_add(Request $request){
        $delivery_note_id = $request->delivery_note_id;
        if($delivery_note_id){
            $delivery_note = DeliveryNote::find($delivery_note_id);
            if($delivery_note){

                $shipment_ids = explode(',', $request->shipment_ids);
                $shipments_verify = explode(',', $request->shipments_verify);
                $verify_count = 0;
                $excess_count = 0;
                $vigilance = new VigilanceVerification();
                $vigilance->delivery_note_id = $delivery_note_id;
                $vigilance->verify_shipments_count = $verify_count;
                $vigilance->excess_shipments_count = $excess_count;
                $vigilance->created_by = Auth::id();
                $vigilance->save();

                foreach ($shipment_ids as $index => $shipment_id){
                    $verify = $shipments_verify[$index];
                    $verify_shipment = new VigilanceVerifiedShipment();
                    $verify_shipment->vigilance_verification_id = $vigilance->id;
                    $verify_shipment->shipment_id = $shipment_id;
                    $verify_shipment->verification_type = $verify;
                    $verify_shipment->save();
                    if($verify == 1){
                        $verify_count++;
                    }
                    else{
                        $excess_count++;
                    }
                }
                $vigilance->verify_shipments_count = $verify_count;
                $vigilance->excess_shipments_count = $excess_count;
                $vigilance->save();
                return redirect()->back()->with('success', 'Vigilance Verified successfully!');
            }
            else{
                return redirect()->back()->with('error', 'Something went wrong, please try again!');
            }
        }
    }

    public function history_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(), 563);
        return view('admin.vigilance.history');
    }
    public function history_list(Request $request){
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 564);
        }

        $deliveries = DeliveryNote::
        join('cities AS oc', 'delivery_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'delivery_notes.rider_id', '=', 'riders.id')
            ->join('routes', 'delivery_notes.route_id', '=', 'routes.id')
            ->leftjoin('admins as ccb', 'delivery_notes.cash_collected_by', '=', 'ccb.id')
            ->join('admins', 'admins.id', '=', 'delivery_notes.admin_id')
            ->leftjoin('admins as ub', 'ub.id', '=', 'delivery_notes.updated_by')
            ->leftjoin('rider_delivery_note_statuses as rdns', 'rdns.delivery_note_id', '=', 'delivery_notes.id')
            ->leftjoin('rider_deliveries as rd', 'rd.delivery_note_id', '=', 'delivery_notes.id')
            ->select(['delivery_notes.id as delivery_note', 'delivery_notes.id as delivery_note_id', 'oc.id as hub_id', 'oc.name as hub', 'riders.name as rider', 'routes.code as route', 'routes.start', 'routes.end', 'admins.name as assignee', 'ub.name as updated_by', 'delivery_notes.updated_at as updated_at', 'delivery_notes.delivered_shipments', 'delivery_notes.delivered_shipments as delivered_shipments_link', 'delivery_notes.created_at', 'delivery_notes.received_cod_amount as amount', 'delivery_notes.shipments_count', 'delivery_notes.shipments_count as shipments_count_link', 'delivery_notes.status', 'delivery_notes.pending_status', 'delivery_notes.cash_collection_status', 'delivery_notes.dncc_status', 'delivery_notes.last_updated_at', 'delivery_notes.cash_collected_by', 'ccb.name as cash_collected', 'delivery_notes.cash_collected_at', 'delivery_notes.special_rider', 'delivery_notes.special_rider_name', 'delivery_notes.special_rider_phone', 'rdns.status as updated_via_app', 'rd.id as rider_delivery_id', 'rd.delivered_status as delivered_status', 'rd.picture_path as picture_path'])
            ->where('riders.operation_rider_id', $request->get('operation_rider_id'))
            ->groupBy('delivery_notes.id');
        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('delivery_notes.hub_id', session('hubs'));
        }

        $datatable = Datatables::of($deliveries)
            ->editColumn('delivery_note', function ($deliveries) {
                $link = "<a href='javascript:void(0);' class='printdeliverynote'><u>" . str_pad($deliveries->delivery_note, 6, '0', STR_PAD_LEFT) . "</u></a>";
                if ($deliveries->pending_status == 1) {
                    $link .= "<br><a href='javascript:void(0);' class='printDNCC'><u>DNCC</u></a>";
                }
                return $link;
            })
            ->editColumn('amount', function ($shipment) {
                return number_format($shipment->amount);
            })
            ->addColumn('delivery_note_id_padded', function ($deliveries) {
                return str_pad($deliveries->delivery_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->filterColumn('delivery_notes.id', function ($query, $keyword) {
                return $query->where('delivery_notes.id', '=', $keyword);
            })
            ->editColumn('shipments_count_link', function ($deliveries) {
                if ($deliveries->shipments_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->shipments_count . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('delivered_shipments_link', function ($deliveries) {
                if ($deliveries->delivered_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->delivered_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('main_status', function ($deliveries) {
                if ($deliveries->status == 0) {
                    if ($deliveries->pending_status == 0) {
                        return 'Pending for Update';
                    } else if ($deliveries->pending_status == 1) {
                        return 'Pending for Verificatin';
                    }
                } else if ($deliveries->status == 1) {
                    if ($deliveries->dncc_status == 1) {
                        return 'Completed';
                    } else if ($deliveries->cash_collection_status == 1) {
                        return 'Cash Collected';
                    } else {
                        return 'Verified';
                    }
                } else if ($deliveries->status == 4) {
                    return 'Canceled';
                }
            })
            ->filterColumn('main_status', function ($query, $keyword) {
                if ($keyword == 0) {
                    $query->where('delivery_notes.pending_status', 0)->where('delivery_notes.status', 0);
                } else if ($keyword == 1) {
                    $query->where('delivery_notes.pending_status', 1)->where('delivery_notes.status', 0);
                } else if ($keyword == 2) {
                    $query->where('delivery_notes.cash_collection_status', 1)->where('delivery_notes.dncc_status', 0);
                } else if ($keyword == 3) {
                    $query->where('delivery_notes.dncc_status', 1)->where('delivery_notes.cash_collection_status', 1);
                } else if ($keyword == 4) {
                    $query->where('delivery_notes.status', 1)->where('delivery_notes.cash_collection_status', 0);
                } else if ($keyword == 5) {
                    $query->where('delivery_notes.status', 4);
                }
            })
            ->editColumn('rider', function ($rider) {
                if ($rider->special_rider) {
                    return $rider->rider . ' (' . $rider->special_rider_name . ')';
                } else {
                    return $rider->rider;
                }
            })
            ->editColumn('route', function ($rider) {
                return $rider->route . ' (' . $rider->start . ' to ' . $rider->end . ')';
            })
            ->filterColumn('route', function ($query, $keyword) {
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('routes.code', 'like', '%' . $keyword . '%')->orWhere('routes.start', 'like', '%' . $keyword . '%')->orWhere('routes.end', 'like', '%' . $keyword . '%');
                } else {
                    $query->whereRaw('false');
                }
            })
            ->editColumn('updated_via_app', function($shipment) {
                if ($shipment->updated_via_app == 1) {
                    return 'Partial';
                } elseif ($shipment->updated_via_app == 2) {
                    return 'Yes';
                } elseif ($shipment->updated_via_app == 0) {
                    return 'No';
                }
                else{
                    return '-';
                }
            })
            ->filterColumn('rdns.status', function ($query, $keyword) {
                if ($keyword != 0) {
                    $query->where('rdns.status', $keyword);
                } else {
                    $query->where('rdns.status' , null)->orWhere('rdns.status',0);
                }
            });
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable->whereBetween('delivery_notes.created_at', [$from, $to]);
        }
        return $datatable->make(true);

    }
}
