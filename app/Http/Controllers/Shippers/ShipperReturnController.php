<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Models\Admin\RcpAssignedAgent;
use App\Http\Models\Admin\RcpAssignedShipment;
use App\Http\Models\Admin\RcpAssignedShipmentLog;
use App\Http\Models\Admin\ReattemptPercentageForShipper;
use App\Http\Models\CityDelivery;
use App\Http\Models\ReturnAssignedShipments;
use App\Http\Models\ShipmentStatusReason;
use App\Http\Models\Admin\AdminRole;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\Blacklist\BlacklistedConsignee;
use App\Http\Models\Blacklist\BlacklistedConsigneeManuallyBlacklisted;
use App\Http\Models\Blacklist\BlacklistSetting;
use App\Http\Models\Blacklist\ConsigneeInformation;
use App\Http\Models\BookingType;
use App\Http\Models\Consolidation;
use App\Http\Models\ConsolidationShipments;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\Shipper\ReturnSheet;
use App\Http\Models\Shipper\ReturnSheetShipments;
use App\Http\Models\ShippingMode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\ReturnAssignedShipmentLogs;
use App\Http\Traits\RvTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class ShipperReturnController extends Controller
{
    use RvTrait;

    public function __construct() {
        $this->middleware('auth:web,substitute_users');

        $this->middleware('Permission');
    }
    public function confirmation_pending_index(){
        $shipment_status = ShipmentStatus::select('id','name')->get();
        $shipping_mode = ShippingMode::all();
        $service_type = BookingType::all();
        $rcp_percent = ReattemptPercentageForShipper::where('user_id',session('user_id'));
        if($rcp_percent->exists())
        {
            $rcp_percent = $rcp_percent->first();
            $rcp_percent = $rcp_percent->percentage;
        }
        else{
            $rcp_percent = 0;
        }
        return view('client.return.confirmation_pending')->with(['shipment_status'=>$shipment_status,'shipping_mode'=>$shipping_mode,'service_type'=>$service_type,'rcp_percent'=>$rcp_percent]);
    }
    public function confirmation_pending_list(Request $request){
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->join('rv_shipment_assign_agents as rsaa' ,'rsaa.shipment_id', '=' , 'shipments.id')
            ->leftJoin('shipping_modes as sm','sm.id','=','shipments.shipping_mode_id')
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
            ->select('shipments.id as shId','shipments.tracking_number','shipments.tracking_number as tracking','u.name as shipper','u.phone as shipper_phone1','u.phone2 as shipper_phone2','oc.name as origin','dc.name as destination','shipments.order_id','h.name as hub','shipments.consignee_name','shipments.consignee_phone_number_1','shipments.consignee_phone_number_2','shipments.consignee_address','shipments.amount','sm.mode','bt.booking_type as service_type','ss.name as status','shipments_journey.remarks as remarks','ssr.id as reason_id','ssr.name as reason','shipments_journey.created_at as status_date','shipments_journey.created_at as last_status_date','sj.created_at as arrival', 'shipments.shipper_status_id as shipper_status_id', 'shipments_journey.shipper_status_id as journey_shipper_status_id', 'dc.pickup as pickup', 'shipments.intercepted as intercepted','shipments.nsa_osa_estimated_charges', 'consolidations.consolidation_id')
            // ->where('shipments.shipper_status_id', DB::raw(12))
            ->where('shipments.shipper_status_id', 65) //shipper advise request
            ->where('rsaa.unresponsive_count', 1) //Unresponsive Count
            ->where('shipments.user_id', session('user_id'))
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
                $route = route('cod.tracking.index');
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
			->addColumn('consolidation', function ($shipments) {
                $consolidations = $this->check_consolidation($shipments->shId);
                $consol = '';
                if ($consolidations) {
                    $consol = $consolidations['order'] . '/' . $consolidations['count'];
                } else {
                    $consol = '-';
                }
                return $consol;
            })
            ->addColumn('consolidated_id', function ($shipments) {
                if ($shipments->consolidation_id) {
                    return $shipments->consolidation_id;
                } else {
                    return '-';
                }
            })
            ->addColumn("action", function ($result) {
                $confirm_button = '<a href="javascript:void(0);" class="dropdown-item returnMarkStatus" data-action="confirm"><i class="ft-plus-circle primary"></i> Confirm</a>';
                $reattempt_button = '<a href="javascript:void(0);" class="dropdown-item returnReattemptStatus"><i class="ft-plus-circle primary"></i> Re-Attempt Request</a>';
                $intercept = '<a href="javascript:void(0);" class="dropdown-item intercept"><i class="ft-plus-circle primary"></i> Intercept/Re-Book</a>';
                $self_collection_button = '<a href="javascript:void(0);" class="dropdown-item selfCollection" data-action="selfCollection"><i class="ft-plus-circle primary"></i> Mark for Self Collection</a>';

                $dropdown = "
                        <div class='btn-group'>
                            <button type='button' class='btn btn-sm btn-success dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Actions</button>
                            <div class='dropdown-menu dropdown-menu-sm'>";
			if (!$result->consolidation_id) {
                if($result->shipper_status_id != 52){
                    $dropdown .= $confirm_button;
                    $dropdown .= $reattempt_button;
                }
				if (($result->shipper_status_id == 12 || $result->shipper_status_id == 52 || $result->shipper_status_id == 65) && $result->journey_shipper_status_id != 53 && $result->intercepted == 0) {
                    $dropdown .= $intercept;
                }
			}
                        
                

                if($result->reason_id == 12){
                    $dropdown .= $self_collection_button;
                }


                $dropdown .= "
                            </div>
                        </div>
                    ";

                return $dropdown;

            })
            ->make(true);
    }
	public function check_consolidation($shipment_id)
    {

        $consolidation_details = array();

        $consolidation_shipment = ConsolidationShipments::where('shipment_id', $shipment_id);

        if ($consolidation_shipment->exists()) {
            $consolidation_shipment = $consolidation_shipment->first();
            $consolidation = Consolidation::find($consolidation_shipment->consolidation_id);
            $consolidation_details['order'] = $consolidation_shipment->order;
            $consolidation_details['consolidation_id'] = $consolidation_shipment->consolidation_id;
            $consolidation_details['count'] = $consolidation->count;
            return $consolidation_details;
        } else {
            return false;
        }
    }
    public function return_reattempt_nsa(Request $request){
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
	
	public function change_status_to_self_collection(Request $request)
    {
        $shipmentId = $request->shipment_id;
        $remark = $request->remark;
        $user_id = session('user_id');
        if ($shipmentId) {
            if (Shipment::where('id', $shipmentId)->where('shipper_status_id', '!=', 15)->exists()) {
                $consolidated_shipments = ConsolidationShipments::where('shipment_id', $shipmentId);
                if ($consolidated_shipments->exists()) {
                    $consolidated_shipments = $consolidated_shipments->first();
                    $all_consolidation_shipments = ConsolidationShipments::where('consolidation_id', $consolidated_shipments->consolidation_id)->pluck('shipment_id')->toArray();
                    Shipment::whereIn('id', $all_consolidation_shipments)->update(['shipper_status_id' => 15, 'consignee_status_id' => 15]);
                    foreach ($all_consolidation_shipments as $shipment) {
                        ShipmentsJourneyController::add($shipment, 15, 15, NULL, $remark, $user_id, NULL);
                    }
                } else {
                    Shipment::where('id', $request->shipment_id)->update(['shipper_status_id' => 15, 'consignee_status_id' => 15]);
                    ShipmentsJourneyController::add($request->shipment_id, 15, 15, NULL, $remark, $user_id, NULL);

                    //update the assigned shipment where rv_assign_agent_status is 7 (Shipper Advised Requested) & rv_state_id is 2 (UnAssigned) update it to completed(4)
                    // $this->shipment_status_update_shipper($request, 7, 2, 4);

                            request()->request->add(['shipment_id' => $shipmentId]);
                            //$updated_type_id updated by shipper = 3
                            //$updated_rv_assign_agent_status_id, on hold i.e is 5
                            //$updated_rv_state_id updating rv status to 4 i.e completed 
                            $this->shipment_status_update_shipper($request, $user_id, 3, 5, 4);
                }

                return ['status' => 0, 'success' => "Shipment status successfully updated to Shipment - On Hold for Self Collection"];
            } else {
                return response()->json(['status' => 1, 'error' => 'Shipment already updated to Shipment - On Hold for Self Collection!']);
            }
        } else {
            return response()->json(['status' => 1, 'error' => 'Shipment ID Not selected!']);
        }
    }

    public function return_marked_single_status(Request $request){

        $this->return_marked_status($request);
        // $parcel = Shipment::find($request->shipment_id);
        // if($parcel){
        //     if($parcel->shipper_status_id == 12){

        //             Shipment::where('id',$request->shipment_id)->update(['shipper_status_id'=>20,'consignee_status_id'=>20]);
        //             $shipment_history = ShipmentsJourney::where('shipment_id',$request->shipment_id)->latest()->first();

        //             ShipmentChargesController::return($request->shipment_id);

        //             AdminFinanceController::add_payment($request->shipment_id, 1);
        //             ShipmentsJourneyController::add($request->shipment_id, 20, 20, $shipment_history->status_reason_id, $request->remark, session('user_id'), NULL);
                
        //         $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $request->shipment_id);
        //         if($return_assign_shipment->exists()){
        //             $return_assign_shipment = $return_assign_shipment->latest()->first();
        //             $return_assign_shipment->status = 0;
        //             $return_assign_shipment->save();

        //             $return_assign_log = new ReturnAssignedShipmentLogs();
        //                     $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
        //                     $return_assign_log->status = 2;
        //                     $return_assign_log->assigned_by = Auth::id();
        //                     $return_assign_log->save();
        //         }

        //         return ['status'=>1,'success'=>"Shipment successfully marked as Shipment - Return Confirm"];

        //     }

        //     return ['status'=>0,'error'=>"Something went wrong, try again later!"];
        // }
        // return ['status'=>0,'error'=>"Something went wrong, try again later!"];
    }

    public function return_marked_status(Request $request)
    {
        // Check if 'shipment_ids' is present in the request
        $shipment_ids = $request->shipment_ids;

        // If 'shipment_ids' exists, process multiple shipments
        if ($shipment_ids) {
            
            foreach ($shipment_ids as $shipment) {
                $parcel = Shipment::find($shipment);

                // Check if the shipper_status_id is not 20 or 52
                if (!in_array($parcel->shipper_status_id, [20, 52])) {
                    $remark_inp = "remark.$shipment";
                    $remarks = ($request->has($remark_inp) && $request->remark[$parcel->id] != null) ? $request->remark[$parcel->id] : null;
                    $shipment_history = ShipmentsJourney::where('shipment_id', $shipment)->latest()->first();

                    Shipment::where('id', $shipment)->update(['shipper_status_id' => 20, 'consignee_status_id' => 20]);
                    ShipmentChargesController::return($shipment);
                    AdminFinanceController::add_payment($shipment, 1);
                    ShipmentsJourneyController::add($shipment, 20, 20, $shipment_history->status_reason_id, $remarks, session('user_id'), NULL);

                    // Update the assigned shipment where rv_assign_agent_status is 7 (Shipper Advised Requested) & rv_state_id is 2 (UnAssigned) update it to completed(4)
                    request()->request->add(['shipment_id' => $shipment]);
                    // $this->shipment_status_update_shipper($request, 7, 2, 4);

                    //$updated_type_id updated by shipper = 3;
                    //$updated_rv_assign_agent_status_id, return confirm i.e is 1 
                    //$updated_rv_state_id updating rv status to 4 i.e completed 
                    $this->shipment_status_update_shipper($request, Auth::id(), 3, 1, 4);
                }
            }

            return ['status' => 1, 'success' => "Shipment successfully marked as Shipment - Return Confirm"];

        } 
        
        else {
            // If 'shipment_ids' is not present, process a single shipment
            $parcel = Shipment::find($request->shipment_id);

            if ($parcel && $parcel->shipper_status_id == 65) {
                Shipment::where('id', $request->shipment_id)->update(['shipper_status_id' => 20, 'consignee_status_id' => 20]);
                $shipment_history = ShipmentsJourney::where('shipment_id', $request->shipment_id)->latest()->first();
                ShipmentChargesController::return($request->shipment_id);
                AdminFinanceController::add_payment($request->shipment_id, 1);
                ShipmentsJourneyController::add($request->shipment_id, 20, 20, $shipment_history->status_reason_id, $request->remark, session('user_id'), NULL);
                
                // Update the assigned shipment where rv_assign_agent_status is 7 (Shipper Advised Requested) & rv_state_id is 2 (UnAssigned) update it to completed(4)
                // $this->shipment_status_update_shipper($request, 7, 2, 4);

                request()->request->add(['shipment_id' => $parcel]);
                //$updated_type_id updated by shipper = 3
                //$updated_rv_assign_agent_status_id, return confirm i.e is 1 
                //$updated_rv_state_id updating rv status to 4 i.e completed 
                $this->shipment_status_update_shipper($request, Auth::id(), 3, 1, 4);

                return response()->json(['status' => 1, 'success' => "Shipment successfully updated as ( Return Confirm )"]);
            }
            return ['status'=> 0, 'error'=>"Something went wrong, try again later!"];
        }
    }

    public function return_reattempt_status(Request $request)
    {
        $shipment_ids = $request->shipment_ids;
        $not_updated_shipments  = array();
        $updated_shipments  = array();
        if (!empty($shipment_ids)) {
            foreach ($shipment_ids as $shipment) {
                $parcel = Shipment::find($shipment);
                if (($parcel->shipper_status_id != 52) && ($parcel->shipper_status_id == 65)) {
                    $journey = ShipmentsJourney::where('shipment_id', $shipment)->where('shipper_status_id', 65)->where('status_reason_id', 12)->latest('id')->first();


                    $remark_inp = "remark.$shipment";

                    $remarks = ($request->has($remark_inp) && $request->remark[$parcel->id] != null) ? $request->remark[$parcel->id] : null;

                    Shipment::where('id', $shipment)->update(['shipper_status_id' => 66, 'consignee_status_id' => 66]);

                    if (session('user_type') != 1) {
                        $reference_1_id = Auth::id();
                    } else {
                        $reference_1_id = null;
                    }
                    $last_reason = ShipmentsJourney::where('shipment_id', $shipment)->orderBy('id', 'DESC');
                    if ($last_reason->exists()) {
                        $last_reason = $last_reason->first();
                        $last_reason_id = $last_reason->status_reason_id;
                    } else {
                        $last_reason_id = NULL;
                    }
                    ShipmentsJourneyController::add($shipment, 52, 52, $last_reason_id, $remarks, session('user_id'), NULL, $reference_1_id);

                    //update the assigned shipment where rv_assign_agent_status is 7 (Shipper Advised Requested) & rv_state_id is 2 (UnAssigned) update it to open(3)
                    // request()->request->add(['shipment_id' => $shipment]);
                    // $this->shipment_status_update_shipper($request, 7, 2, 3);

                    request()->request->add(['shipment_id' => $parcel]);
                    //$updated_type_id updated by shipper = 3
                    //$updated_rv_assign_agent_status_id, reattempt i.e is 2 
                    //$updated_rv_state_id updating rv status to 4 i.e completed 
                    $this->shipment_status_update_shipper($request, Auth::id(), 3, 2, 4);


                    if ($parcel->shipper_status_id == 12 && ($journey['status_reason_id'] == 12)) {
                        NotificationsController::send(33, $shipment);
                    }
                    $updated_shipments[] = $parcel->tracking_number;
                } else {
                    $not_updated_shipments[] = $parcel->tracking_number;
                }
            }
            return response()->json(['status' => 1, 'not_updated_shipments' => $not_updated_shipments, 'updated_shipments' => $updated_shipments, 'success' => "Shipments has been requested for Re-Attempt, Please note that this is subjected to final confirmation by Customer Experience!"]);
        }
    }


    //This function is now used as shipper request for Reattempt Request Button  
    public function return_reattempt_single_status(Request $request)
    {
        $parcel = Shipment::find($request->shipment_id);
        if ($parcel) {
            if ($parcel->shipper_status_id != 52) {
                if ($parcel->shipper_status_id == 65) {
                    $journey = ShipmentsJourney::where('shipment_id', $request->shipment_id)->where('shipper_status_id', 65)->where('status_reason_id', 12)->latest('id')->first();
                    // Shipment::where('id',$request->shipment_id)->update(['shipper_status_id' => 52,'consignee_status_id' => 52]);

                    //Update shipment status id to 66 (Shipment - Re-Attempt Call Requested)
                    Shipment::where('id', $request->shipment_id)->update(['shipper_status_id' => 66, 'consignee_status_id' => 66]);

                    if (session('user_type') != 1) {
                        $reference_1_id = Auth::id();
                    } else {
                        $reference_1_id = null;
                    }
                    $last_reason = ShipmentsJourney::where('shipment_id', $parcel->id)->orderBy('id', 'DESC');
                    if ($last_reason->exists()) {
                        $last_reason = $last_reason->first();
                        $last_reason_id = $last_reason->status_reason_id;
                    } else {
                        $last_reason_id = NULL;
                    }
                    // ShipmentsJourneyController::add($request->shipment_id, 52, 52, $last_reason_id, $request->remark, session('user_id'), NULL, $reference_1_id);

                    //Update shipment status id to 66 (Shipment - Re-Attempt Call Requested)
                    ShipmentsJourneyController::add($request->shipment_id, 66, 66, $last_reason_id, $request->remark, session('user_id'), NULL, $reference_1_id);

                    //update the assigned shipment where rv_assign_agent_status is 7 (Shipper Advised Requested) & rv_state_id is 2 (UnAssigned) update it to open(3)
                    // $this->shipment_status_update_shipper($request, 7, 2, 3);



                    request()->request->add(['shipment_id' => $parcel]);
                    //$updated_type_id updated by shipper = 3
                    //$updated_rv_assign_agent_status_id, reattempt requested i.e is 2
                    //$updated_rv_state_id updating rv status to 3 i.e open 
                    $this->shipment_status_update_shipper($request, Auth::id(), 3, 2, 3);

                    if ($journey) {
                        NotificationsController::send(33, $request->shipment_id);
                    }

                    return response()->json(['status' => 1, 'success' => "Shipment has been requested for Re-Attempt, Please note that this is subjected to final confirmation by Customer Experience!"]);
                } else {
                    return ['status' => 0, 'error' => "Shipment is already updated for Re-attempt!"];
                }
            }
            return ['status' => 0, 'error' => "Shipment is already updated, Please check tracking!"];
        }
        return ['status' => 0, 'error' => "Something went wrong, try again later!"];
    }


    public function return_reattempt_history_index(){
        $shipment_status = ShipmentStatus::select('id','name')->get();
        $shipping_mode = ShippingMode::all();
        return view('client.return.reattempt_history')->with(['shipment_status'=>$shipment_status,'shipping_mode'=>$shipping_mode]);
    }

    public function return_reattempt_history_list(Request $request){
        $shipments_journey = ShipmentsJourney::join('shipments as s', 's.id', '=', 'shipments_journey.shipment_id')
            ->join('users as u', 's.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 's.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 's.consignee_city_id', '=', 'dc.id')
            ->leftJoin('shipping_modes as sm','sm.id','=','s.shipping_mode_id')
            ->leftJoin('booking_types as bt','bt.id','=','s.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','s.shipper_status_id')
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 's.id')
                    ->where('sj.created_at','=',
                        DB::raw('(select max(created_at) from shipments_journey where shipments_journey.shipment_id = s.id)'));
            })
            ->select('s.tracking_number as tracking_number','s.tracking_number as tracking','u.name as shipper','oc.name as origin','dc.name as destination','s.consignee_name','s.consignee_phone_number_1','s.consignee_phone_number_2','s.consignee_address','s.amount','sm.mode','bt.booking_type as service_type','ss.name as current_status','sj.created_at as current_status_date','shipments_journey.created_at as reattempt_status_date','sj.remarks as current_remarks')
            ->whereIn('shipments_journey.shipper_status_id', [66, 52])
            ->where('s.user_id', session('user_id'));

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
                $route = route('cod.tracking.index');
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
            ->make(true);
    }
    public function blacklist_search_consignee(Request $request){
        $phone = $request->phone;
        $data = array();
        $consignee_information = ConsigneeInformation::where('phone', $phone);
        if($consignee_information->exists()){
            $consignee_information = $consignee_information->first();
            $data['consignee'] = array();

            $data['consignee']['id'] = $consignee_information->id;
            $data['consignee']['name'] = $consignee_information->name;
            $data['consignee']['phone'] = $consignee_information->phone;
            $data['consignee']['phone2'] = $consignee_information->phone2;
            $data['consignee']['address'] = $consignee_information->address;
            $data['consignee']['city'] = $consignee_information->consignee_city->name;
            $consignee_information_id = $consignee_information->id;
            $manual_blacklist = BlacklistedConsigneeManuallyBlacklisted::where('consignee_information_id', $consignee_information_id);
            $color = NULL;
            if($manual_blacklist->exists()){
                $manual_blacklist = $manual_blacklist->first();
                $color = BlacklistSetting::find($manual_blacklist->blacklist_setting_id)->color;
            }
            if(BlacklistedConsignee::where('consignee_information_id', $consignee_information_id)->exists()){
                $data['blacklist'] = array();
                $data['blacklist']['total_shipments'] = $consignee_information->blacklisted_consignee->shipments;
                $data['blacklist']['delivered'] = $consignee_information->blacklisted_consignee->delivered;
                $data['blacklist']['delivered_ratio'] = $consignee_information->blacklisted_consignee->delivered_ratio;
                $data['blacklist']['undelivered'] = $consignee_information->blacklisted_consignee->undelivered;
                $data['blacklist']['undelivered_ratio'] = $consignee_information->blacklisted_consignee->undelivered_ratio;
                $data['blacklist']['return'] = $consignee_information->blacklisted_consignee->return;
                $data['blacklist']['return_ratio'] = $consignee_information->blacklisted_consignee->return_ratio;
                if($color == NULL){
                    $data['blacklist']['color'] = $consignee_information->blacklisted_consignee->blacklist->color;
                }else{
                    $data['blacklist']['color'] = $color;
                }
            }
            return response()->json(['status' => 0, 'success' => 'Consignee information found!', 'details' => $data]);
        }
        return response()->json(['status' => 1, 'error' => 'Consignee not found']);
    }
    public function return_confirmed_index()
    {
        $blacklists = BlacklistSetting::select(['id', 'name'])->where('status', 1)->get();
        $shipment_status = ShipmentStatus::select('id','name')->get();
        $shipping_mode = ShippingMode::all();
        $service_type = BookingType::all();
        $return_confirm_reason_ids = DB::table('shipment_status_shipment_status_reason')->where('shipment_status_id', 20)->pluck('shipment_status_reason_id')->toArray();

        $return_confirm_reasons = ShipmentStatusReason::whereIn('id', $return_confirm_reason_ids)->select('id', 'name')->get();
        $agents = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')
            ->where('admin_roles.department_id',3)->get();

        return view('client.return.confirm')->with(['shipment_status'=>$shipment_status,'shipping_mode'=>$shipping_mode,'service_type'=>$service_type, 'return_confirm_reasons' => $return_confirm_reasons, 'agents' => $agents, 'blacklists' => $blacklists]);
        ;

    }
    public function return_list(Request $request)
    {
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->leftJoin('shipping_modes as sm','sm.id','=','shipments.shipping_mode_id')
            ->leftJoin('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftjoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where('shipments_journey.shipper_status_id','=',13)
                    ->where('shipments_journey.verification','=',1);
            })
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipment_status_reason as ssr','ssr.id','=','shipments_journey.status_reason_id')
            ->select('shipments.id as shId','shipments.tracking_number','shipments.tracking_number as tracking','u.name as shipper','u.phone as shipper_phone1','u.phone2 as shipper_phone2','oc.name as origin','dc.name as destination','shipments.order_id','h.name as hub','shipments.consignee_name','shipments.consignee_phone_number_1','shipments.consignee_phone_number_2','shipments.consignee_address','shipments.amount','sm.mode','bt.booking_type as service_type','ss.name as status','ssr.id as reason_id','ssr.name as reason','shipments_journey.remarks as remarks','shipments_journey.created_at as status_date','shipments_journey.created_at as last_status_date','sj.created_at as arrival', 'shipments.booking_type_id', 'usi.poc', 'shipments_journey.remarks as shipper_remarks','shipments.shipper_status_id as current_status_id','shipments.nsa_osa_estimated_charges', 'shipments_journey.shipper_status_id as journey_shipper_status_id', 'dc.pickup as pickup', 'shipments.intercepted as intercepted','dc.id as consignee_city_id','shipments.shipping_mode_id')
            ->where('shipments.user_id', session('user_id'))
            ->where('shipments.shipper_status_id', DB::raw(20))
            ->groupBy('shipments.id');
        if(session('user_type') == 2){
            if(session('restriction') == 1){
                $shipments = $shipments->join('substitute_user_shipments as sus', function($join){
                    $join->on('sus.shipment_id', '=', 'shipments.id')
                        ->where('sus.substitute_user_id', '=', Auth::id());
                });
            }
        }

        $datatable = Datatables::of($shipments)
            ->setRowAttr([
                'class' => function ($shipments) {
                    if ($shipments->complaint != null) {
                        return 'complaint_row';
                    }
                    if($shipments->reason_id == 12){
                        return 'nsa_osa_reason';
                    }
                    if ($shipments->current_status_id == 52) {
                        return 'goldClass';
                    }else if($shipments->booking_type_id == 3){
                        return "tnb_row";
                    }
                },
                'consolidation_id' => function($shipments){
                    if($shipments->consolidation_id != null){
                        return $shipments->consolidation_id;
                    } else {
                        return '';
                    }
                }
            ])
            ->editColumn('tracking_number',function ($shipments){
                $route = route('cod.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->editColumn('shipper_phone',function ($shipper){
                return "$shipper->shipper_phone1 | $shipper->shipper_phone2";
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
            ->addColumn('consignee_phone',function ($shipper){
                $consignee_phone = '';
                $consignee_phone .= $shipper->consignee_phone_number_1;
                if($shipper->consignee_phone_number_2 != null){
                    $consignee_phone .= "| ".$shipper->consignee_phone_number_2;
                }

                return $consignee_phone;

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
            ->orderColumn('consignee_phone', 'shipments.consignee_phone_number_1 $1, shipments.consignee_phone_number_2 $1')

            ->addColumn('shipment_remarks',function ($shipments){
                $remark = $shipments->remarks;
                if($remark != null){
                    return $remark;
                }
                else{
                    return '-';
                }
            })
            ->editColumn('status_date',function ($shipments){
                if($shipments->status_date) {
                    if (2 - ((new \Carbon\Carbon($shipments->status_date, 'UTC'))->diffInDays()) < 0) {
                        return "<span class='danger font-weight-bold'>" . $shipments->status_date . "</span>";
                    } else {
                        return $shipments->status_date;
                    }
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
                $open_intercept = CityDelivery::where('city_id', $result->consignee_city_id)->where('shipping_mode_id',$result->shipping_mode_id)->exists();
                $confirm_button = '<a href="javascript:void(0);" class="dropdown-item returnMarkStatus" data-action="confirm"><i class="ft-plus-circle primary"></i> Confirm</a>';
                $re_attempt_button = '<a href="javascript:void(0);" class="dropdown-item returnMarkStatus" data-action="reattempt"><i class="ft-plus-circle primary"></i> Re-Attempt</a>';
                $intercept = '<a href="javascript:void(0);" class="dropdown-item intercept"><i class="ft-plus-circle primary"></i> Intercept/Re-Book</a>';
                $self_collection_button = '<a href="javascript:void(0);" class="dropdown-item selfCollection" data-action="selfCollection"><i class="ft-plus-circle primary"></i> Mark for Self Collection</a>';
                $edit_estimate_charges = '<a href="javascript:void(0);" class="dropdown-item editEstimateCharges" data-action="editEstimateCharges"><i class="ft-plus-circle primary"></i> Edit Estimate Charges</a>';

                $dropdown = "
                    <div class='btn-group'>
                       <button type='button' class='btn btn-sm btn-success dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Actions</button>
                        <div class='dropdown-menu dropdown-menu-sm'>";

                $dropdown .= $confirm_button;

                $dropdown .= $re_attempt_button;

                if($result->reason_id == 12 || $result->current_status_id == 52){
                    $dropdown .= $self_collection_button;
                }

                if($result->reason_id == 12 || $result->current_status_id == 52){
                    $dropdown .= $edit_estimate_charges;
                }
                if(($result->current_status_id == 12 || $result->current_status_id == 52) && $result->journey_shipper_status_id != 53 && $result->intercepted == 0){
                    if($open_intercept){
                        $dropdown .= $intercept;
                    }
                }

                $dropdown .= "
                        </div>
                    </div>
                ";

                    return $dropdown;
            });
        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $datatable->whereIn('shipments.tracking_number', explode(',', $tracking_numbers));
        }
        if($mode = $request->get('search_shipping_mode')){
            $datatable->where('sm.id', '=', $mode);
        }
        return $datatable->make(true);

    }
    public function return_sheet_pending_index()
    {
        $shipment_status = ShipmentStatus::select('id','name')->whereIn('id', [23, 24, 25, 28, 29, 31, 34, 35, 38, 47, 48,60])->get();
        $shipping_mode = ShippingMode::all();
        $service_type = BookingType::all();
        return view('client.return.sheet.pending')->with(['shipment_status'=>$shipment_status,'shipping_mode'=>$shipping_mode,'service_type'=>$service_type]);
    }
    public function return_sheet_pending_list(Request $request)
    {
        $shipments = ReturnSheet::join('shipments as s', 's.id', '=', 'return_sheets.shipment_id')
            ->join('user_shipping_infos AS usi', 's.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 's.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->leftJoin('shipping_modes as sm','sm.id','=','s.shipping_mode_id')
            ->leftJoin('booking_types as bt','bt.id','=','s.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','s.shipper_status_id')
            ->join('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 's.id')
                    ->where('shipments_journey.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = s.id and verification = 1)'));
            })
            ->select('s.id as shId','s.tracking_number','s.tracking_number as tracking','oc.name as origin','dc.name as destination','s.order_id','h.name as hub','s.consignee_name','s.consignee_phone_number_1','s.consignee_phone_number_2','s.consignee_address','s.amount','sm.mode','bt.booking_type as service_type','ss.name as status','shipments_journey.remarks as remarks','shipments_journey.created_at as status_date', 'usi.poc', 'shipments_journey.remarks as shipper_remarks')
            ->where('return_sheets.user_id', session('user_id'))
            ->where('return_sheets.status_id', DB::raw(0));
        if(session('user_type') == 2){
            if(session('restriction') == 1){
                $shipments = $shipments->join('substitute_user_shipments as sus', function($join){
                    $join->on('sus.shipment_id', '=', 's.id')
                        ->where('sus.substitute_user_id', '=', Auth::id());
                });
            }
        }

        $datatable = Datatables::of($shipments)
            ->editColumn('tracking_number',function ($shipments){
                $route = route('cod.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->addColumn('consignee_phone',function ($shipper){
                $consignee_phone = '';
                $consignee_phone .= $shipper->consignee_phone_number_1;
                if($shipper->consignee_phone_number_2 != null){
                    $consignee_phone .= "| ".$shipper->consignee_phone_number_2;
                }

                return $consignee_phone;

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
            ->orderColumn('consignee_phone', 'shipments.consignee_phone_number_1 $1, shipments.consignee_phone_number_2 $1')

            ->addColumn('shipment_remarks',function ($shipments){
                $remark = $shipments->remarks;
                if($remark != null){
                    return $remark;
                }
                else{
                    return '-';
                }
            });
        return $datatable->make(true);
    }

    public function return_sheet_receive_index()
    {
        return view('client.return.sheet.receive');
    }

    public function return_sheet_receive_shipment_info(Request $request)
    {
        $return_statuses = array(25, 31, 38 ,23, 28, 34);
        $tracking_number = $request->tracking;
        $shipment = Shipment::where('tracking_number', $tracking_number)->where('user_id', session('user_id'));
        if($shipment->exists()){
            $shipment = $shipment->first();
            if(in_array($shipment->shipper_status_id, $return_statuses)){
                $return_sheet = ReturnSheet::where('shipment_id', $shipment->id);
                if($return_sheet->exists()){
                    $return_sheet = $return_sheet->first();
                    if($return_sheet->status_id == 0){
                        return response()->json(['status' => 1, 'shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'destination' => $shipment->consignee_city->name, 'consignee_name' => $shipment->consignee_name, 'phone' => $shipment->consignee_phone_number_1, 'address' => $shipment->consignee_address, 'amount' => ($shipment->amount), 'shipment_status' => $shipment->status_shipper->name]);
                    }
                    else{
                        return response()->json(['status' => 0, 'error' => 'Shipment is already received with remarks ' . $return_sheet->remarks]);
                    }
                }
                else{
                    return response()->json(['status' => 0, 'error' => 'Shipment is not ready to be received!']);
                }
            }
            else{
                return response()->json(['status' => 0, 'error' => 'Shipment is not ready to be received!']);
            }
        }
        else{
            return response()->json(['status' => 0, 'error' => 'Shipment with given Tracking Number not Found!']);
        }

    }

    public function return_sheet_receive_submit(Request $request)
    {
        $received_by = '';
        if(session('user_type') == 2){
            $received_by = ' (Substitute User)';
        }
        $shipment_ids = explode(',', $request->shipment_ids);
        foreach($shipment_ids as $shipment_id){
            $ReturnSheetShipments = new ReturnSheetShipments();
            $return_sheet = ReturnSheet::where('shipment_id', $shipment_id);
            if($return_sheet->exists()){
                $return_sheet = $return_sheet->first();
                $return_sheet->status_id = 1;
                $return_sheet->received_at = Carbon::now();
                $return_sheet->remarks = 'Received By ' . Auth::user()->name . $received_by;
                $return_sheet->save();

            
                $ReturnSheetShipments->shipment_id = $shipment_id;
                $ReturnSheetShipments->scan_via = 1;
                $ReturnSheetShipments->return_sheet_id = $return_sheet->id;
                $ReturnSheetShipments->save();

            }
        }
        return redirect()->route('cod.return.sheet.history.index')->with('success', 'Shipment Received Successfully!');
    }
    
    public function return_sheet_history_index()
    {
        $shipment_status = ShipmentStatus::select('id','name')->whereIn('id', [23, 24, 25, 28, 29, 31, 34, 35, 38, 47, 48,60])->get();
        $shipping_mode = ShippingMode::all();
        $service_type = BookingType::all();
        return view('client.return.sheet.history')->with(['shipment_status'=>$shipment_status,'shipping_mode'=>$shipping_mode,'service_type'=>$service_type]);
    }
    
    public function return_sheet_history_list(Request $request)
    {
        $shipments = ReturnSheet::join('shipments as s', 's.id', '=', 'return_sheets.shipment_id')
            ->join('user_shipping_infos AS usi', 's.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 's.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->leftJoin('shipping_modes as sm','sm.id','=','s.shipping_mode_id')
            ->leftJoin('booking_types as bt','bt.id','=','s.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','s.shipper_status_id')
            ->join('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 's.id')
                    ->where('shipments_journey.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = s.id and verification = 1)'));
            })
            ->select('s.id as shId','s.tracking_number','s.tracking_number as tracking','oc.name as origin','dc.name as destination','s.order_id','h.name as hub','s.consignee_name','s.consignee_phone_number_1','s.consignee_phone_number_2','s.consignee_address','s.amount','sm.mode','bt.booking_type as service_type','ss.name as status','shipments_journey.remarks as remarks','shipments_journey.created_at as status_date', 'usi.poc', 'shipments_journey.remarks as shipper_remarks', 'return_sheets.remarks as received_remarks', 'return_sheets.received_at as received_date')
            ->where('return_sheets.user_id', session('user_id'))
            ->whereIn('return_sheets.status_id', [DB::raw(1), DB::raw(2)]);
        if(session('user_type') == 2){
            if(session('restriction') == 1){
                $shipments = $shipments->join('substitute_user_shipments as sus', function($join){
                    $join->on('sus.shipment_id', '=', 's.id')
                        ->where('sus.substitute_user_id', '=', Auth::id());
                });
            }
        }

        $datatable = Datatables::of($shipments)
            ->editColumn('tracking_number',function ($shipments){
                $route = route('cod.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->addColumn('consignee_phone',function ($shipper){
                $consignee_phone = '';
                $consignee_phone .= $shipper->consignee_phone_number_1;
                if($shipper->consignee_phone_number_2 != null){
                    $consignee_phone .= "| ".$shipper->consignee_phone_number_2;
                }

                return $consignee_phone;

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
            ->orderColumn('consignee_phone', 'shipments.consignee_phone_number_1 $1, shipments.consignee_phone_number_2 $1')

            ->addColumn('shipment_remarks',function ($shipments){
                $remark = $shipments->remarks;
                if($remark != null){
                    return $remark;
                }
                else{
                    return '-';
                }
            });
        return $datatable->make(true);
    }
}
