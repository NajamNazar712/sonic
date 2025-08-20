<?php

namespace App\Http\Controllers\Retail;

use App\Http\Models\Admin\RcpAssignedAgent;
use App\Http\Models\Admin\RcpAssignedShipment;
use App\Http\Models\Admin\RcpAssignedShipmentLog;
use App\Http\Traits\RvTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\BookingType;
use App\Http\Models\ReturnAssignedShipmentLogs;
use App\Http\Models\ReturnAssignedShipments;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\ShippingMode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class RetailReturnController extends Controller
{
    use RvTrait;
    public function __construct()
    {
        $this->middleware('auth:retail');

    }

    public function confirmation_pending(){
        $shipment_status = ShipmentStatus::select('id','name')->get();
        $shipping_mode = ShippingMode::all();
        $service_type = BookingType::all();
        return view('retail.return.confirmation_pending',compact('shipment_status','service_type','shipping_mode'));
    }

    public function confirmation_pending_list(Request $request){
        $from = $request->search_date_from ? Carbon::parse($request->search_date_from)->toDateString() : null;
        $to = $request->search_date_to ? Carbon::parse($request->search_date_to)->toDateString() : null;
        
        if (!$from && !$to) {
            $from = Carbon::now()->toDateString();
            $to = Carbon::now()->subMonths(3)->toDateString();
        } elseif (!$from) {
            $from = Carbon::now()->toDateString();
        } elseif (!$to) {
            $to = Carbon::now()->toDateString();
        }

        $shipments = Shipment::join('retail_shipments as rs', 'rs.shipment_id', '=', 'shipments.id')
            ->join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->join('shipping_modes as sm','sm.id','=','shipments.shipping_mode_id')
            ->leftJoin('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftJoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where('shipments_journey.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipment_status_reason as ssr','ssr.id','=','shipments_journey.status_reason_id')
			->leftjoin('consolidation_shipments as consolidations', function ($join) {
                $join->on('consolidations.shipment_id', '=', 'shipments.id')
                    ->where('consolidations.consolidation_id', '=',
                        DB::raw('(select consolidation_id from consolidation_shipments where consolidation_shipments.shipment_id = shipments.id)'));
            })
            ->select('rs.retail_user_id as retail_user_id','shipments.id as shId','shipments.tracking_number','shipments.tracking_number as tracking','u.name as shipper','u.phone as shipper_phone1','u.phone2 as shipper_phone2','oc.name as origin','dc.name as destination','shipments.order_id','h.name as hub','shipments.consignee_name','shipments.consignee_phone_number_1','shipments.consignee_phone_number_2','shipments.consignee_address','shipments.amount','sm.mode','bt.booking_type as service_type','ss.name as status','shipments_journey.remarks as remarks','ssr.id as reason_id','ssr.name as reason','shipments_journey.created_at as status_date','shipments_journey.created_at as last_status_date','sj.created_at as arrival', 'shipments.shipper_status_id as shipper_status_id', 'shipments_journey.shipper_status_id as journey_shipper_status_id', 'dc.pickup as pickup', 'shipments.intercepted as intercepted','shipments.nsa_osa_estimated_charges', 'consolidations.consolidation_id')
            ->where('shipments.shipper_status_id', DB::raw(12))
            ->where('rs.retail_user_id', Auth::id())
            ->whereBetween('rs.created_at', [$from, $to])
            ->groupBy('shipments.id');

            if(session('user_type') == 2){
                if(session('restriction') == 1){
                    $shipments = $shipments->join('substitute_user_shipments as sus', function($join){
                        $join->on('sus.shipment_id', '=', 'shipments.id')
                            ->where('sus.substitute_user_id', '=', Auth::id());
                    });
                }
            }

        return Datatables::of($shipments)
            ->setRowAttr([
                'class' => function ($shipments) {
                    if ($shipments->reason_id == 12) {
                        return 'nsa_osa_reason';
                    }
                },
                'consolidation_id' => function ($shipments) {
                    if ($shipments->consolidation_id != null) {
                        return $shipments->consolidation_id;
                    } else {
                        return '';
                    }
                }
            ])
            ->editColumn('tracking_number',function ($shipments){
                $route = route('retail.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->editColumn('consignee_phone', function ($shipments) {
                return '<button type="button" class="btn btn-sm btn-outline-info align-middle consignee_info_label" rel="'. $shipments->consignee_phone_number_1 .'"><i class="la la-lg la-phone align-middle"></i> <span class="align-middle">' . $shipments->consignee_phone_number_1 . '|' . $shipments->consignee_phone_number_2 .'</span></button>';
            })
            ->filterColumn('consignee_phone',function ($query,$keyword){
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('shipments.consignee_phone_number_1', 'like', '%'.$keyword.'%')->orWhere('shipments.consignee_phone_number_2', 'like', '%'.$keyword.'%');
                }

                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('shipment_remarks',function ($shipments){
                $remark = '<textarea style="width:200px;" placeholder="Enter Remarks" class="form-control form-control-sm" rows="4" cols="100" >'.$shipments->remarks.'</textarea>';
                return $remark;
            })
            ->orderColumn('consignee_phone', 'shipments.consignee_phone_number_1 $1, shipments.consignee_phone_number_2 $1')
            ->editColumn('arrival',function($shipments){
                if($shipments->arrival){
                    return $shipments->arrival;
                }else{
                    return " - ";
                }
            })
            ->filterColumn('status',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('ss.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn("action", function ($result) {
                $reattempt_button = '<a href="javascript:void(0);" class="dropdown-item returnReattemptStatus"><i class="ft-plus-circle primary"></i> Re-Attempt Request</a>';

                $dropdown = "
                        <div class='btn-group'>
                            <button type='button' class='btn btn-sm btn-success dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Actions</button>
                            <div class='dropdown-menu dropdown-menu-sm'>";
			if (!$result->consolidation_id) {
                if($result->shipper_status_id != 52){
                    $dropdown .= $reattempt_button;
                }
				
			}

                $dropdown .= "
                            </div>
                        </div>
                    ";

                return $dropdown;

            })
            ->rawColumns(['tracking_number', 'consignee_phone', 'shipment_remarks', 'action'])
            ->make(true);
    }

    public function pending_reattempt_nsa(Request $request){
        $shipment_ids = $request->shipment_ids;
        if($request->single == 1){
            $shipment = Shipment::where('id', $shipment_ids)->first();
            if($shipment['shipper_status_id'] == 12){
                $shipment_journey = ShipmentsJourney::where(['shipment_id' => $shipment_ids, 'shipper_status_id' => 12])->first();
                if($shipment_journey['status_reason_id'] == 12){
                    $nsa_shipment = $shipment->tracking_number;
                    $nsa_shipments_id = (int)$shipment_ids;
                    if($shipment->nsa_osa_estimated_charges){
                        $estimated_charge = $shipment->nsa_osa_estimated_charges;
                    }
                    else{
                        $estimated_charge = '-';
                    }
                }
            }
            if(isset($nsa_shipment) && $nsa_shipment != null){
                return ['status' => 1, 'nsa_shipment' => $nsa_shipment, 'estimated_charge' => $estimated_charge, 'nsa_shipments_id' => $nsa_shipments_id];
            }
            else{
                return ['status' => 0];
            }
        }
        else{
            $estimated_charges = array();
            $nsa_shipments = array();
            foreach ($shipment_ids as $shipment_id){
                $shipment = Shipment::where('id', $shipment_id)->first();
                if($shipment['shipper_status_id'] == 12){
                    $shipment_journey = ShipmentsJourney::where(['shipment_id' => $shipment_id, 'shipper_status_id' => 12])->first();
                    if($shipment_journey['status_reason_id'] == 12){
                        $nsa_shipments[$shipment_id] = $shipment->tracking_number;
                        $nsa_shipments_ids[] = (int)$shipment_id;
                        if($shipment->nsa_osa_estimated_charges){
                            $estimated_charges[$shipment_id] = $shipment->nsa_osa_estimated_charges;
                        }
                        else{
                            $estimated_charges[$shipment_id] = '-';
                        }
                    }
                }
            }
            if(isset($nsa_shipment) && $nsa_shipments != null){
                return ['status' => 1, 'nsa_shipments' => $nsa_shipments, 'estimated_charges' => $estimated_charges, 'nsa_shipments_ids' => $nsa_shipments_ids];
            }
            else{
                return ['status' => 0];
            }
        }
    }

    public function mark_reattempt(Request $request){
        $parcel = Shipment::find($request->shipment_id);
        if($parcel){
            // if($parcel->shipper_status_id != 52){
            if($parcel->shipper_status_id != 52 || $parcel->shipper_status_id != 66){
                if($parcel->shipper_status_id == 12){
                    // $journey = ShipmentsJourney::where('shipment_id', $request->shipment_id)->where('shipper_status_id', 12)->where('status_reason_id', 12)->latest('id')->first();
                    // Shipment::where('id',$request->shipment_id)->update(['shipper_status_id' => 52,'consignee_status_id' => 52]);


                    $journey = ShipmentsJourney::where('shipment_id', $request->shipment_id)->where('shipper_status_id', 12)->where('status_reason_id', 12)->latest('id')->first();
                    Shipment::where('id',$request->shipment_id)->update(['shipper_status_id' => 66,'consignee_status_id' => 66]);

                    if (session('user_type') != 1) {
                        $reference_1_id = Auth::id();
                    }
                    else{
                        $reference_1_id = null;
                    }
                    $last_reason = ShipmentsJourney::where('shipment_id', $parcel->id)->orderBy('id', 'DESC');
                    if($last_reason->exists()){
                        $last_reason = $last_reason->first();
                        $last_reason_id = $last_reason->status_reason_id;
                    }
                    else{
                        $last_reason_id = NULL;
                    }
                    // ShipmentsJourneyController::add($request->shipment_id, 52, 52, $last_reason_id, $request->remark, session('user_id'), NULL, $reference_1_id);

                    //Update shipment status id to 66 (Shipment - Re-Attempt Call Requested)
                    ShipmentsJourneyController::add($request->shipment_id, 66, 66, $last_reason_id, $request->remark, session('user_id'), NULL, $reference_1_id);
                    
                   $rcp_assigned_shipment = RcpAssignedShipment::where('shipment_id', $request->shipment_id)->where('assigned_status', 1)->where('shipment_status', 0);
                   if ($rcp_assigned_shipment->exists()) {
                       $rcp_assigned_shipment = $rcp_assigned_shipment->latest()->first();
                       $rcp_assigned_shipment->shipment_status = 3; //reattempt status
                       $rcp_assigned_shipment->admin_id = Auth::id();
                       $rcp_assigned_shipment->save();

                       //updating already_updated & pending of agent if shipment is updated by shipper 
                       $rcp_assigned_agent = RcpAssignedAgent::where('id',$rcp_assigned_shipment->rcp_assigned_agent_id)->first();
                       $already_updated = $rcp_assigned_agent->increment('already_updated');
                       $rcp_assigned_agent->decrement('pending_shipments');
                       $rcp_assigned_agent->save();


                       $return_assign_log = new RcpAssignedShipmentLog ();
                       $return_assign_log->rcp_assigned_shipment_id = $rcp_assigned_shipment->id;
                       $return_assign_log->shipment_id = $rcp_assigned_shipment->shipment_id;
                       $return_assign_log->status = 3; //reattempt status
                       $return_assign_log->admin_id = Auth::id();
                       $return_assign_log->save();
                   }

                   $request->merge(['shipment_id' => $request->shipment_id]);
                    //$updated_type_id updated by retail = 5
                    //$updated_rv_assign_agent_status_id, reattempt requested i.e is 2
                    //$updated_rv_state_id updating rv status to 3 i.e open 
                    $this->shipment_status_update_shipper($request, Auth::id(), 5, 2, 3);
                   
                    if($journey){
                        NotificationsController::send(33, $request->shipment_id);
                    }

                    return response()->json(['status'=>1,'success'=>"Shipment has been requested for Re-Attempt, Please note that this is subjected to final confirmation by Customer Experience!"]);
                }
                else{
                    return ['status'=>0,'error'=>"Shipment is already updated for Re-attempt!"];
                }
            }
            return ['status'=>0,'error'=>"Shipment is already updated, Please check tracking!"];

        }
        return ['status'=>0,'error'=>"Something went wrong, try again later!"];
    }

    public function reattempt_history(){
        $shipment_status = ShipmentStatus::select('id','name')->get();
        $shipping_mode = ShippingMode::all();
        return view('retail.return.reattempt_history')->with(['shipment_status'=>$shipment_status,'shipping_mode'=>$shipping_mode]);
    }

    public function reattempt_history_list(Request $request){
        $from = $request->search_date_from ? Carbon::parse($request->search_date_from)->toDateString() : null;
        $to = $request->search_date_to ? Carbon::parse($request->search_date_to)->toDateString() : null;
        
        if (!$from && !$to) {
            $from = Carbon::now()->toDateString();
            $to = Carbon::now()->subMonths(3)->toDateString();
        } elseif (!$from) {
            $from = Carbon::now()->toDateString();
        } elseif (!$to) {
            $to = Carbon::now()->toDateString();
        }

        $shipments_journey = ShipmentsJourney::join('shipments as s', 's.id', '=', 'shipments_journey.shipment_id')
            ->join('retail_shipments as rs', 'rs.shipment_id', '=', 'shipments_journey.shipment_id')
            ->join('users as u', 's.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 's.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 's.consignee_city_id', '=', 'dc.id')
            ->join('shipping_modes as sm','sm.id','=','s.shipping_mode_id')
            ->leftJoin('booking_types as bt','bt.id','=','s.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','s.shipper_status_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 's.id')
                    ->where('sj.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = s.id)'));
            })
            ->select('rs.retail_user_id as retail_user_id','s.tracking_number as tracking_number','s.tracking_number as tracking','u.name as shipper','oc.name as origin','dc.name as destination','s.consignee_name','s.consignee_phone_number_1','s.consignee_phone_number_2','s.consignee_address','s.amount','sm.mode','bt.booking_type as service_type','ss.name as current_status','sj.created_at as current_status_date','shipments_journey.created_at as reattempt_status_date','sj.remarks as current_remarks')
            ->where('shipments_journey.shipper_status_id', 52)
            ->whereBetween('rs.created_at', [$from, $to])
            ->where('rs.retail_user_id', Auth::id());

            if(session('user_type') == 2){
                if(session('restriction') == 1){
                    $shipments_journey = $shipments_journey->join('substitute_user_shipments as sus', function($join){
                        $join->on('sus.shipment_id', '=', 's.id')
                            ->where('sus.substitute_user_id', '=', Auth::id());
                    });
                }
            }
        return Datatables::of($shipments_journey)
            ->editColumn('tracking_number',function ($shipments){
                $route = route('retail.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->editColumn('consignee_phone',function ($shipper){
                if($shipper->consignee_phone_number_2 != null){
                    $phone = "$shipper->consignee_phone_number_1 | $shipper->consignee_phone_number_2";
                }
                else{
                    $phone = $shipper->consignee_phone_number_1;
                }
                return $phone;
            })
            ->filterColumn('consignee_phone',function ($query,$keyword){
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('s.consignee_phone_number_1', 'like', '%'.$keyword.'%')->orWhere('s.consignee_phone_number_2', 'like', '%'.$keyword.'%');
                }

                else {
                    $query->whereRaw('false');
                }
            })
            ->orderColumn('consignee_phone', 's.consignee_phone_number_1 $1, s.consignee_phone_number_2 $1')
            ->filterColumn('current_status',function ($query,$keyword){
                if ($keyword != '') {
                    $query->where('ss.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->rawColumns(['tracking_number'])
            ->make(true);
    }

}
