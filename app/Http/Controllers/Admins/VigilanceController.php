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
use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Models\ShipmentsJourney;
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
        if (session('role_id') != 1) {
            $riders = Rider::join('cities as c', 'c.id', '=', 'riders.city_id')->select('riders.id', 'riders.name', 'riders.trax_id', 'c.name as rider_city')->where('riders.status', 1)->whereIn('c.hub_id', session('hubs'))->get();
        } else {
            $riders = Rider::where('status', 1)->get();
        }
        return view('admin.vigilance.verification')->with(['riders' => $riders]);
    }

    public function verification_list(Request $request){
        $delivery_note = DeliveryNote::join('delivery_note_shipments as dns', 'dns.delivery_note_id', '=', 'delivery_notes.id')
            ->join('riders as r', 'r.id', '=', 'delivery_notes.rider_id')
            ->join('cities as c', 'c.id', '=', 'r.city_id')
            ->join('shipments as s', 's.id', '=', 'dns.shipment_id')
            ->join('users as u', 'u.id', '=', 's.user_id')
            ->join('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 's.id')
                    ->where('shipments_journey.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = s.id and shipments_journey.shipper_status_id = 5)'));
            })
            ->join('shipment_status as ss', 'ss.id', '=', 's.shipper_status_id')
            ->select('s.tracking_number', 's.id as shipment_id', 'delivery_notes.id as delivery_note_id', 'ss.name as shipment_status', 'shipments_journey.created_at as status_date', 'r.name as rider', 'u.name as shipper', 's.amount')
        ->where('delivery_notes.status', 0)
        ->whereDate('delivery_notes.created_at', Carbon::today());

        if (session('role_id') != 1) {
            $delivery_note = $delivery_note->whereIn('c.hub_id', session('hubs'));
        }

        if ($delivery_note_id = $request->get('search_delivery_note_id')) {

            $delivery_note->where('delivery_notes.id', '=', $delivery_note_id);
        }


        if ($rider_id = $request->get('search_rider')) {

            $delivery_note->where('delivery_notes.rider_id', '=', $rider_id);
        }

        if(($request->get('search_delivery_note_id') == NULL) && ($request->get('search_rider') == NULL)){

            $delivery_note->whereRaw('false');

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
                $delivery_note = $delivery_note->get()->first();
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
                    $data['assignee'] = $delivery_note->admin->name;
                    $data['created_at'] = Carbon::parse($delivery_note->created_at)->toDateTimeString();
                    
                    $delivery_note_shipment = DeliveryNoteShipment::where('delivery_note_id', $delivery_note_id)->where('shipment_id', $shipment->id);
                    if($delivery_note_shipment->exists()){
                        $data['verify'] = 'Verified';
                        $data['verify_id'] = 1;
                    }
                    else{
                        $data['verify'] = 'Excess';
                        $data['verify_id'] = 2;
                    }
                    
                    $data['shipment_status'] =  $shipment->status_shipper->name;
                    $last_status = ShipmentsJourney::where('shipment_id',$shipment->id)->orderBy('id','desc')->first();
                    $last_status_date = Carbon::parse($last_status->created_at)->toDateTimeString();
                    $data['status_date'] = $last_status_date;
                    
                    ShipmentScanningJourneyController::add($shipment->id,30,1,Auth::id(),NULL,NULL);

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

        $deliveries = VigilanceVerification::
            join('delivery_notes', 'delivery_notes.id', '=', 'vigilance_verifications.delivery_note_id')
            ->join('admins AS ad', 'delivery_notes.admin_id', '=', 'ad.id')
            ->join('cities AS oc', 'delivery_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'delivery_notes.rider_id', '=', 'riders.id')
            ->join('routes', 'delivery_notes.route_id', '=', 'routes.id')
            ->leftjoin('admins as cb', 'cb.id', '=', 'vigilance_verifications.created_by')
            ->select(['delivery_notes.id as delivery_note_id', 'oc.id as hub_id', 'oc.name as hub', 'riders.name as rider', 'routes.code as route', 'routes.start', 'routes.end', 'cb.name as created_by', 'vigilance_verifications.id as vigilance_id', 'vigilance_verifications.created_at as created_at', 'vigilance_verifications.verify_shipments_count', 'vigilance_verifications.excess_shipments_count', 'delivery_notes.shipments_count', 'delivery_notes.status', 'delivery_notes.pending_status', 'delivery_notes.cash_collection_status', 'delivery_notes.dncc_status', 'delivery_notes.special_rider_name','delivery_notes.created_at as asigned_date','ad.name as asignee', DB::raw(' `shipments_count` - `verify_shipments_count` AS  unverify_shipments_order')]);
        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('delivery_notes.hub_id', session('hubs'));
        }

        $datatable = Datatables::of($deliveries)
            ->addColumn('delivery_note', function ($deliveries) {
                $link = "<a href='javascript:void(0);' class='printdeliverynote' noteId='". $deliveries->delivery_note_id ."'><u>" . str_pad($deliveries->delivery_note_id, 6, '0', STR_PAD_LEFT) . "</u></a>";
                if ($deliveries->pending_status == 1) {
                    $link .= "<br><a href='javascript:void(0);' class='printDNCC'><u>DNCC</u></a>";
                }
                return $link;
            })
            ->addColumn('delivery_note_id_padded', function ($deliveries) {
                return str_pad($deliveries->delivery_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->filterColumn('delivery_notes.id', function ($query, $keyword) {
                return $query->where('delivery_notes.id', '=', $keyword);
            })
            ->addColumn('shipments_count_link', function ($deliveries) {
                if ($deliveries->shipments_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle" noteId="'. $deliveries->delivery_note_id .'">' . $deliveries->shipments_count . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('excess_shipments_link', function ($deliveries) {
                if ($deliveries->shipments_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle" noteId="'. $deliveries->delivery_note_id .'">' . $deliveries->excess_shipments_count . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('verify_shipments_link', function ($deliveries) {
                if ($deliveries->shipments_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle" noteId="'. $deliveries->delivery_note_id .'">' . $deliveries->verify_shipments_count . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('unverify_shipments', function ($deliveries) {
                if ($deliveries->shipments_count != 0) {
                    return $deliveries->shipments_count - $deliveries->verify_shipments_count ;
                } else {
                    return 0;
                }
            })
            ->addColumn('unverify_shipments_link', function ($deliveries) {
                if ($deliveries->shipments_count-$deliveries->verify_shipments_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle" noteId="'. $deliveries->delivery_note_id .'">' . ($deliveries->shipments_count-$deliveries->verify_shipments_count) . '</button>';
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
            });
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable->whereBetween('vigilance_verifications.created_at', [$from, $to]);
        }
        return $datatable->make(true);

    }
    public function verification_excess_cns(Request $request){
        $verify_id = $request->verify_id;
        if($verify_id){
            $vigilance = VigilanceVerification::find($verify_id);
            if($vigilance){
                $excess_count = $vigilance->excess_shipments_count;
                if($excess_count != 0){
                    $shipments = array();
                    $vigilance_shipments = VigilanceVerifiedShipment::where('vigilance_verification_id', $vigilance->id)->where('verification_type', 2)->get();
                    if($vigilance_shipments->count() != 0){
                        foreach ($vigilance_shipments as $vigilance_shipment) {
                            $shipment = Shipment::find($vigilance_shipment->shipment_id);
                            $shipments[] = $shipment->tracking_number;
                        }
                        return ['status' => 0, 'success' => 'Excess Shipments', 'shipments' => $shipments];
                    }
                    else{
                        return ['status' => 0, 'success' => 'No Excess Shipments', 'shipments' => FALSE];
                    }
                }
            }
        }
    }

    public function verification_verify_cns(Request $request){
        $verify_id = $request->verify_id;
        if($verify_id){
            $vigilance = VigilanceVerification::find($verify_id);
            if($vigilance){
                $verify_count = $vigilance->verify_shipments_count;
                if($verify_count != 0){
                    $shipments = array();
                    $vigilance_shipments = VigilanceVerifiedShipment::where('vigilance_verification_id', $vigilance->id)->where('verification_type', 1)->get();
                    if($vigilance_shipments->count() != 0){
                        foreach ($vigilance_shipments as $vigilance_shipment) {
                            $shipment = Shipment::find($vigilance_shipment->shipment_id);
                            $shipments[] = $shipment->tracking_number;
                        }
                        return ['status' => 0, 'success' => 'Verify Shipments', 'shipments' => $shipments];
                    }
                    else{
                        return ['status' => 0, 'success' => 'No Verify Shipments', 'shipments' => FALSE];
                    }
                }
            }
        }
    }

    public function verification_unverify_cns(Request $request){
        $verify_id = $request->verify_id;
        if($verify_id){
            $vigilance = VigilanceVerification::find($verify_id);
            if($vigilance){
                $verify_count = $vigilance->verify_shipments_count;
                if($verify_count != 0){
                    $shipments = array();

                    $vigilance_shipments = VigilanceVerifiedShipment::where('vigilance_verification_id',$vigilance->id)->where('verification_type',1)->pluck('shipment_id')->toArray();
                    $delivery_note_shipments = DeliveryNoteShipment::where('delivery_note_id',$vigilance->delivery_note_id)->pluck('shipment_id')->toArray();
                    
                    if($request->unverify_shipments_count < 0){
                        
                        $diff_shipments = array_diff($vigilance_shipments,$delivery_note_shipments);
                    }else{

                        $diff_shipments = array_diff($delivery_note_shipments,$vigilance_shipments);
                    }
                        if(count($diff_shipments) != 0){
                            foreach ($diff_shipments as $diff_shipment) {
                                $shipment = Shipment::find($diff_shipment);
                                $shipments[] = $shipment->tracking_number;
                            }
                            return ['status' => 0, 'success' => 'Unverify Shipments', 'shipments' => $shipments];
                        }
                        else{
                            return ['status' => 0, 'success' => 'No Unverify Shipments', 'shipments' => FALSE];
                        }

                }
            }
        }

    }
}
