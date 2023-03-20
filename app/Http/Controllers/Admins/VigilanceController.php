<?php

namespace App\Http\Controllers\Admins;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\DeliveryNote;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\Admin\ReturnNoteShipment;
use App\Http\Models\Admin\Vigilance\VigilanceNote;
use App\Http\Models\Admin\Vigilance\VigilanceNoteShipment;
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
        return redirect()->to(route('admin.vigilance.note.index'));
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

    public function vigilance_note_index(Request $request){
        ActivityTrailController::createActivityTrailLog(Auth::id(), 562);
        if (session('role_id') != 1) {
            $riders = Rider::join('cities as c', 'c.id', '=', 'riders.city_id')->select('riders.id', 'riders.name', 'riders.trax_id', 'c.name as rider_city')->where('riders.status', 1)->whereIn('c.hub_id', session('hubs'))->get();
        } else {
            $riders = Rider::where('status', 1)->get();
        }
        return view('admin.vigilance.note')->with(['riders' => $riders]);
    }

    public function vigilance_note_list(Request $request){
        if($request->search_note_type == 1){
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
                ->select('s.tracking_number', 's.id as shipment_id', 'delivery_notes.id as note_id', 'ss.name as shipment_status', 'shipments_journey.created_at as status_date', 'r.name as rider', 'u.name as shipper', 's.amount')
                ->where('delivery_notes.status', 0);

            if (session('role_id') != 1) {
                $delivery_note = $delivery_note->whereIn('c.hub_id', session('hubs'));
            }


            if ($rider_id = $request->get('search_rider')) {

                $delivery_note->where('delivery_notes.rider_id', '=', $rider_id);
            }

            if($request->get('search_rider') == NULL){

                $delivery_note->whereRaw('false');

            }

            $datatables = Datatables::of($delivery_note)
                ->addColumn('tracking_number_link', function ($shipments) {
                    $route = route('admin.tracking.index');
                    return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
                })
                ->addColumn('note', function ($deliveries) {
                    return str_pad($deliveries->note_id, 6, '0', STR_PAD_LEFT);
                });

            return $datatables->make(true);
        }
        else{
            $return_note = ReturnNote::join('return_note_shipments as rns', 'rns.return_note_id', '=', 'return_notes.id')
                ->join('riders as r', 'r.id', '=', 'return_notes.rider_id')
                ->join('cities as c', 'c.id', '=', 'r.city_id')
                ->join('shipments as s', 's.id', '=', 'rns.shipment_id')
                ->join('users as u', 'u.id', '=', 's.user_id')
                ->join('shipments_journey', function ($join) {
                    $join->on('shipments_journey.shipment_id', '=', 's.id')
                        ->where('shipments_journey.id', '=',
                            DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = s.id and shipments_journey.shipper_status_id in (23, 28))'));
                })
                ->join('shipment_status as ss', 'ss.id', '=', 's.shipper_status_id')
                ->select('s.tracking_number', 's.id as shipment_id', 'return_notes.id as note_id', 'ss.name as shipment_status', 'shipments_journey.created_at as status_date', 'r.name as rider', 'u.name as shipper', 's.amount')
                ->where('return_notes.status', 0);

            if (session('role_id') != 1) {
                $return_note = $return_note->whereIn('c.hub_id', session('hubs'));
            }


            if ($rider_id = $request->get('search_rider')) {

                $return_note->where('return_notes.rider_id', '=', $rider_id);
            }

            if($request->get('search_rider') == NULL){

                $return_note->whereRaw('false');

            }

            $datatables = Datatables::of($return_note)
                ->addColumn('tracking_number_link', function ($shipments) {
                    $route = route('admin.tracking.index');
                    return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
                })
                ->addColumn('note', function ($return) {
                    return str_pad($return->note_id, 6, '0', STR_PAD_LEFT);
                });

            return $datatables->make(true);
        }

    }

    public function vigilance_note_info(Request $request){
        $rider_id = $request->rider_id;
        if($rider_id){
            $tracking_number = $request->tracking_number;
            $shipment = Shipment::where('tracking_number', $tracking_number);
            if($shipment->exists()){
                $shipment = $shipment->first();
                $data = array();
                $data['id'] = $shipment->id;
                $data['tracking_number'] = $shipment->tracking_number;
                $data['amount'] = $shipment->amount;
                $data['origin'] = $shipment->pickup_address->city->name;
                $data['destination'] = $shipment->consignee_city->name;

                if($request->note_type == 1){
                    $delivery_note = DeliveryNoteShipment::join('delivery_notes', 'delivery_notes.id', '=', 'delivery_note_shipments.delivery_note_id')
                        ->where('delivery_notes.status', 0)->where('delivery_notes.rider_id', $rider_id)->where('delivery_note_shipments.shipment_id', $shipment->id);
                    if($delivery_note->exists()){
                        $delivery_note = $delivery_note->select('delivery_notes.created_at', 'delivery_notes.admin_id', 'delivery_notes.id as delivery_note_id')->first();
                        $data['assignee'] = Admin::find($delivery_note->admin_id)->name;
                        $data['created_at'] = Carbon::parse($delivery_note->created_at)->toDateTimeString();
                        $data['verify'] = 'Verified';
                        $data['verify_id'] = 1;
                        $data['note'] = str_pad($delivery_note->delivery_note_id, 6, '0', STR_PAD_LEFT);
                    }
                    else{
                        $data['verify'] = 'Excess';
                        $data['verify_id'] = 2;
                        $data['assignee'] = '';
                        $data['created_at'] = '';
                        $data['note'] = '';
                    }

                }
                else{

                    $return_note = ReturnNoteShipment::join('return_notes', 'return_notes.id', '=', 'return_note_shipments.return_note_id')
                        ->where('return_notes.status', 0)->where('return_notes.rider_id', $rider_id)->where('return_note_shipments.shipment_id', $shipment->id);
                    if($return_note->exists()){
                        $return_note = $return_note->select('return_notes.created_at', 'return_notes.admin_id', 'return_notes.id as return_note_id')->first();
                        $data['assignee'] = Admin::find($return_note->admin_id)->name;
                        $data['created_at'] = Carbon::parse($return_note->created_at)->toDateTimeString();
                        $data['verify'] = 'Verified';
                        $data['verify_id'] = 1;
                        $data['note'] = str_pad($return_note->return_note_id, 6, '0', STR_PAD_LEFT);
                    }
                    else{
                        $data['verify'] = 'Excess';
                        $data['verify_id'] = 2;
                        $data['assignee'] = '';
                        $data['created_at'] = '';
                        $data['note'] = '';
                    }


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
        return response()->json(['status' => 0, 'error' => 'Invalid Rider ID!']);
    }

    public function vigilance_note_add(Request $request){

        $rider_id = $request->rider_id;
        if($rider_id){
            $note_type_id = $request->note_type;
            $shipment_ids = explode(',', $request->shipment_ids);
            $shipments_verify = explode(',', $request->shipments_verify);
            $verify_count = 0;
            $excess_count = 0;
            $total_shipments_count = 0;

            $vigilance = new VigilanceNote();
            $vigilance->rider_id = $rider_id;
            $vigilance->vigilance_note_type_id = $note_type_id;
            $vigilance->total_shipments_count = $total_shipments_count;
            $vigilance->verify_shipments_count = $verify_count;
            $vigilance->excess_shipments_count = $excess_count;
            $vigilance->created_by = Auth::id();
            $vigilance->save();

            if($note_type_id == 1){
                $delivery_note = DeliveryNoteShipment::join('delivery_notes', 'delivery_notes.id', '=', 'delivery_note_shipments.delivery_note_id')
                    ->where('delivery_notes.status', 0)->where('delivery_notes.rider_id', $rider_id)->whereIn('delivery_note_shipments.shipment_id', $shipment_ids);

                if($delivery_note->exists()){
                    $delivery_note = $delivery_note->select('delivery_notes.id as delivery_note_id', 'delivery_note_shipments.shipment_id')->get();
                    $delivery_note_ids = array();
                    $delivery_note_shipments = array();
                    foreach ($delivery_note as $index => $note){
                        $delivery_note_shipments[$note->shipment_id] = $note->delivery_note_id;
                        if(!in_array($note->delivery_note_id, $delivery_note_ids)){
                            $delivery_note_ids[] = $note->delivery_note_id;
                        }
                    }
                    $note_shipments_count = DeliveryNote::whereIn('id', $delivery_note_ids)->select(DB::raw('SUM(shipments_count) as shipments_count'))->first();

                    $total_shipments_count = $note_shipments_count->shipments_count;

                    foreach ($shipment_ids as $index => $shipment_id){
                        $verify = $shipments_verify[$index];
                        $verify_shipment = new VigilanceNoteShipment();
                        $verify_shipment->vigilance_note_id = $vigilance->id;
                        $verify_shipment->note_id = array_key_exists($shipment_id, $delivery_note_shipments)? $delivery_note_shipments[$shipment_id]:null;
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
                    $vigilance->total_shipments_count = $total_shipments_count;
                    $vigilance->verify_shipments_count = $verify_count;
                    $vigilance->excess_shipments_count = $excess_count;
                    $vigilance->save();
                    return redirect()->back()->with('success', 'Vigilance Note created successfully!');
                }
                else{
                    return redirect()->back()->with('error', 'Something went wrong, please try again!');
                }
            }
            else if($note_type_id == 2){
                $return_note = ReturnNoteShipment::join('return_notes', 'return_notes.id', '=', 'return_note_shipments.return_note_id')
                    ->where('return_notes.status', 0)->where('return_notes.rider_id', $rider_id)->whereIn('return_note_shipments.shipment_id', $shipment_ids);

                $return_note = $return_note->select('return_notes.id as return_note_id', 'return_note_shipments.shipment_id')->get();
                $return_note_ids = array();
                $return_note_shipments = array();
                foreach ($return_note as $index => $note){
                    $return_note_shipments[$note->shipment_id] = $note->return_note_id;
                    if(!in_array($note->return_note_id, $return_note_ids)){
                        $return_note_ids[] = $note->return_note_id;
                    }
                }
                $note_shipments_count = ReturnNote::whereIn('id', $return_note_ids)->select(DB::raw('SUM(shipments_count) as shipments_count'))->first();

                $total_shipments_count = $note_shipments_count->shipments_count;
                foreach ($shipment_ids as $index => $shipment_id){
                    $verify = $shipments_verify[$index];
                    $verify_shipment = new VigilanceNoteShipment();
                    $verify_shipment->vigilance_note_id = $vigilance->id;
                    $verify_shipment->note_id = array_key_exists($shipment_id, $return_note_shipments)? $return_note_shipments[$shipment_id]:null;
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
                $vigilance->total_shipments_count = $total_shipments_count;
                $vigilance->verify_shipments_count = $verify_count;
                $vigilance->excess_shipments_count = $excess_count;
                $vigilance->save();

                return redirect()->back()->with('success', 'Vigilance Note created successfully!');

            }
            else{
                return redirect()->back()->with('error', 'Something went wrong, please try again!');
            }

        }
        return redirect()->back()->with('error', 'Something went wrong, please try again!');
    }

    public function vigilance_note_history_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(), 563);
        return view('admin.vigilance.note_history');
    }
    public function vigilance_note_history_list(Request $request){
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 564);
        }

        $vigilance = VigilanceNote::leftjoin('riders', 'vigilance_notes.rider_id', '=', 'riders.id')
            ->leftjoin('rider_types as rt', 'rt.id', '=', 'riders.rider_type_id')
            ->leftjoin('admins AS cb', 'vigilance_notes.created_by', '=', 'cb.id')
            ->leftjoin('vigilance_note_types as vnt', 'vnt.id', '=', 'vigilance_notes.vigilance_note_type_id')
            ->leftjoin('cities AS rc', 'riders.city_id', '=', 'rc.id')
            ->leftjoin('cities AS h', 'rc.hub_id', '=', 'h.id')
            ->select('vigilance_notes.id as vigilance_note_id', 'h.id as hub_id', 'h.name as hub', 'riders.name as rider', 'cb.name as created_by', 'vigilance_notes.created_at as created_at', 'vigilance_notes.verify_shipments_count', 'vigilance_notes.excess_shipments_count', 'vigilance_notes.total_shipments_count', 'rt.name as rider_type', 'vnt.name as note_type');
        if (session('role_id') != 1) {
            $vigilance = $vigilance->whereIn('h.id', session('hubs'));
        }

        $datatable = Datatables::of($vigilance)
            ->addColumn('vigilance_note_id_padded', function ($vigilance) {
                return str_pad($vigilance->vigilance_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->filterColumn('note', function ($query, $keyword) {
                return $query->where('delivery_notes.id', '=', $keyword);
            })
            ->addColumn('shipments_count_link', function ($vigilance) {
                if ($vigilance->total_shipments_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle" noteId="'. $vigilance->vigilance_note_id .'">' . $vigilance->total_shipments_count . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('excess_shipments_link', function ($vigilance) {
                if ($vigilance->excess_shipments_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle" noteId="'. $vigilance->vigilance_note_id .'">' . $vigilance->excess_shipments_count . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('verify_shipments_link', function ($vigilance) {
                if ($vigilance->verify_shipments_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle" noteId="'. $vigilance->vigilance_note_id .'">' . $vigilance->verify_shipments_count . '</button>';
                } else {
                    return 0;
                }
            });
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatable->whereBetween('vigilance_notes.created_at', [$from, $to]);
        }

        if ($tracking_number = $request->get('search_tracking_no')) {
            $datatable->join('vigilance_note_shipments as vns', 'vigilance_notes.id', '=', 'vns.vigilance_note_id')
                ->join('shipments as s', 'vns.shipment_id', '=', 's.id')
                ->where('s.tracking_number', '=', $tracking_number);
        }

        if ($delivery_note = $request->get('search_delivery_note')) {
            $datatable->leftjoin('vigilance_note_shipments as vns', 'vigilance_notes.id', '=', 'vns.vigilance_note_id')
                ->where('vns.note_id', '=', $delivery_note)
                ->where('vigilance_notes.vigilance_note_type_id', 1)
            ->groupBy('vigilance_notes.id');
        }
        if ($return_note = $request->get('search_return_note')) {
            $datatable->leftjoin('vigilance_note_shipments as vns', 'vigilance_notes.id', '=', 'vns.vigilance_note_id')
                ->where('vns.note_id', '=', $return_note)
                ->where('vigilance_notes.vigilance_note_type_id', 2)
                ->groupBy('vigilance_notes.id');;
        }

        return $datatable->make(true);

    }

    public function vigilance_note_total_shipments(Request $request){
        $vigilance_note_id = $request->input('vigilance_note_id');
        $vigilance_note_shipments = VigilanceNoteShipment::where('vigilance_note_id', $vigilance_note_id)->get();

        $data = array();
        if ($vigilance_note_shipments->count() > 0) {
            foreach ($vigilance_note_shipments as $vigilance_note_shipment) {
                if(array_key_exists($vigilance_note_shipment->note_id, $data)){
                    $data[$vigilance_note_shipment->note_id][] = $vigilance_note_shipment->shipment_id;
                }
                else{
                    $data[$vigilance_note_shipment->note_id] = [$vigilance_note_shipment->shipment_id];
                }
            }
            if(count($data) > 0){
                foreach ($data as $note_id => $note){
                    $data[$note_id] = Shipment::whereIn('id', $note)->pluck('tracking_number')->toArray();
                }

                return ['status' => 0, 'success' => 'Note Shipments', 'shipments' => $data];
            }
            else{
                return ['status' => 0, 'success' => 'No Shipments Found', 'shipments' => FALSE];
            }
        } else {
            return ['status' => 0, 'success' => 'No Delivery / Return Note Shipments', 'shipments' => FALSE];
        }
    }

    public function vigilance_note_excess_shipments(Request $request){
        $vigilance_note_id = $request->input('vigilance_note_id');
        $vigilance_note_shipments = VigilanceNoteShipment::where('vigilance_note_id', $vigilance_note_id)->where('verification_type', 2)->pluck('shipment_id')->toArray();

        $data = array();
        if (count($vigilance_note_shipments) > 0) {
            $data[] = Shipment::whereIn('id', $vigilance_note_shipments)->pluck('tracking_number')->toArray();
            return ['status' => 0, 'success' => 'Delivery / Return Note Shipments', 'shipments' => $data];
        } else {
            return ['status' => 0, 'success' => 'No Delivery / Return Note Shipments', 'shipments' => FALSE];
        }
    }

    public function vigilance_note_verify_shipments(Request $request){
        $vigilance_note_id = $request->input('vigilance_note_id');
        $vigilance_note_shipments = VigilanceNoteShipment::where('vigilance_note_id', $vigilance_note_id)->where('verification_type', 1)->get();

        $data = array();
        if ($vigilance_note_shipments->count() > 0) {
            foreach ($vigilance_note_shipments as $vigilance_note_shipment) {
                if(array_key_exists($vigilance_note_shipment->note_id, $data)){
                    $data[$vigilance_note_shipment->note_id][] = $vigilance_note_shipment->shipment_id;
                }
                else{
                    $data[$vigilance_note_shipment->note_id] = [$vigilance_note_shipment->shipment_id];
                }
            }
            if(count($data) > 0){
                foreach ($data as $note_id => $note){
                    $data[$note_id] = Shipment::whereIn('id', $note)->pluck('tracking_number')->toArray();
                }

                return ['status' => 0, 'success' => 'Note Shipments', 'shipments' => $data];
            }
            else{
                return ['status' => 0, 'success' => 'No Shipments Found', 'shipments' => FALSE];
            }
        } else {
            return ['status' => 0, 'success' => 'No Delivery / Return Note Shipments', 'shipments' => FALSE];
        }
    }
}
