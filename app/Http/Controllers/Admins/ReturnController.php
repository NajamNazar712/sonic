<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Admins\ActivityTrailController;
use App\Http\Controllers\Admins\AdminFinanceController;
use App\Http\Controllers\Admins\ShipmentChargesController;
use App\Http\Controllers\EmployeeAttendanceController;
use App\Http\Controllers\ShipmentScanningJourneyController;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ShipmentOpenBoxJourneyController;
use App\Http\Models\Admin\AdminRole;
use App\Http\Models\Admin\DeliveryNoteShipment;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\ReturnNote;
use App\Http\Models\Admin\ReturnNoteImage;
use App\Http\Models\Admin\ReturnNoteShipment;
use App\http\Models\Admin\ReturnReasonMandatoryShipper;
use App\Http\Models\Admin\ReturnReattemptRatio;
use App\Http\Models\Blacklist\BlacklistSetting;
use App\Http\Models\BookingType;
use App\Http\Models\City;
use App\Http\Models\ConsolidationShipments;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\EmployeeDeviceToken;
use App\Http\Models\PackagingMaterialRequest;
use App\Http\Models\PackagingMaterialRequestDetail;
use App\Http\Models\PackagingMaterialRequestHistory;
use App\Http\Models\PendingPayment;
use App\Http\Models\PendingPaymentShipment;
use App\Http\Models\ReturnAssignedShipments;
use App\Http\Models\Rider;
use App\Http\Models\Route;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentPiece;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\ShipmentStatusReason;
use App\Http\Models\Shipper\ReturnSheet;
use App\Http\Models\Shipper\User;
use App\Http\Models\ShippingMode;
use App\Http\Models\Warehouse\WarehouseFulfilmentHubs;
use App\Http\Models\WarehouseStock;
use App\Http\Models\CityDelivery;
use App\Http\Models\Zone;
use App\Jobs\RCPSmsToConsignee;
use App\ReturnConfirmationPendingSmsAttempt;
use Carbon\Carbon;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\AgentReturnConfirmation;
use App\Http\Models\ReturnAssignedShipmentLogs;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\Datatables\Datatables;
use Yajra\Datatables\Services\DataTable;
use function foo\func;
use App\Http\Models\Shipper\UserShippingInfo;
use Illuminate\Support\Str;
use App\Http\Models\Admin\NonServiceArea;
use App\Http\Models\Admin\OsaChargesLog;
use App\Http\Models\Admin\ReattemptShipmentStatusRemarks;
use App\Http\Models\Admin\ReturnRevertLog;
use App\Http\Models\ConsigneeRefusedReason;

class ReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    public function return_view(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),26);
        $blacklists = BlacklistSetting::select(['id', 'name'])->where('status', 1)->get();
        $shipment_status = ShipmentStatus::select('id','name')->get();
        $shipping_mode = ShippingMode::all();
        $service_type = BookingType::all();
        $return_confirm_reason_ids = DB::table('shipment_status_shipment_status_reason')->where('shipment_status_id', 20)->whereNotIn('shipment_status_reason_id', [2, 55])->pluck('shipment_status_reason_id')->toArray();

        $return_confirm_reasons = ShipmentStatusReason::whereIn('id', $return_confirm_reason_ids)->select('id', 'name')->get();
        $consignee_refused_reasons = ConsigneeRefusedReason::where('status', 1)->select('id', 'reasons')->where('status', 1)->get();

        $agents = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')
            ->where('admin_roles.department_id',3)
            ->where('a.status',1)->get();
        return view('admin.return.index')->with(['shipment_status'=>$shipment_status,'shipping_mode'=>$shipping_mode,'service_type'=>$service_type, 'return_confirm_reasons' => $return_confirm_reasons, 'agents' => $agents, 'blacklists' => $blacklists, 'consignee_refused_reasons' => $consignee_refused_reasons]);
    }

    public function return_marked_list(Request $request){ //status 12 shipments
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),86);
        }

        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->leftjoin('rcp_tat_options as tat_options','tat_options.id','=','u.rcp_tat_option_id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->join('shipping_modes as sm','sm.id','=','shipments.shipping_mode_id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftJoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where('shipments_journey.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->leftJoin('shipments_journey as admin_journey', function ($join) {
                $join->on('admin_journey.shipment_id', '=', 'shipments.id')
                    ->where('admin_journey.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id != 52)'));
            })
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftJoin('shipments_journey as sret', function ($join) {
                $join->on('sret.shipment_id', '=', 'shipments.id')
                    ->where('sret.shipper_status_id','=',13)
                    ->where('sret.verification','=',1);
//                    ->where('sret.id','=',
//                        DB::raw('(select id from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 13)'));
            })
            ->leftJoin('shipment_status_reason as ssr','ssr.id','=','shipments_journey.status_reason_id')
            ->leftjoin('crm_requests as crm', function ($join) {
                $join->on('crm.shipment_id', '=', 'shipments.id')
                    ->where('crm.id','=',
                        DB::raw('(select max(id) from crm_requests where crm_requests.shipment_id = shipments.id)'));
            })
            ->leftjoin('return_assigned_shipments as ras', function ($join) {
                $join->on('ras.shipment_id', '=', 'shipments.id')
                    ->where('ras.id','=',
                        DB::raw('(select max(id) from return_assigned_shipments where return_assigned_shipments.shipment_id = shipments.id and return_assigned_shipments.status = 1)'));
            })
            ->leftjoin('admins as asad', 'asad.id', '=', 'ras.admin_id')
            ->leftjoin('admins as asadby', 'asadby.id', '=', 'ras.assigned_by')
			->leftjoin('consolidation_shipments as consolidations', function ($join){
                $join->on('consolidations.shipment_id', '=', 'shipments.id')
                    ->where('consolidations.consolidation_id','=',
                        DB::raw('(select consolidation_id from consolidation_shipments where consolidation_shipments.shipment_id = shipments.id)'));
            })
           /* ->leftjoin('return_confirmation_pending_sms_attempts as rcps','rcps.shipment_id','=','shipments.id')*/
            ->select('shipments.id as shId','shipments.tracking_number','shipments.tracking_number as tracking','u.name as shipper','u.phone as shipper_phone1','u.phone2 as shipper_phone2','oc.name as origin','dc.name as destination','shipments.order_id','h.name as hub','shipments.consignee_name','shipments.consignee_phone_number_1','shipments.consignee_phone_number_2','shipments.consignee_address as consignee_address','shipments.amount','sm.mode','bt.booking_type as service_type','ss.name as status','ssr.id as reason_id','ssr.name as reason','admin_journey.remarks as remarks','shipments_journey.created_at as status_date','shipments_journey.created_at as last_status_date','sj.created_at as arrival', 'shipments.booking_type_id', 'usi.vendor as vendor_name', 'usi.poc', DB::raw('count(sret.shipment_id) as reattempts'), 'shipments_journey.remarks as shipper_remarks','shipments.shipper_status_id as current_status_id','crm.id as complaint','shipments.nsa_osa_estimated_charges', 'shipments_journey.shipper_status_id as journey_shipper_status_id', 'dc.pickup as pickup', 'shipments.intercepted as intercepted','dc.id as consignee_city_id','shipments.shipping_mode_id', 'asad.name as assigned_agent', 'ras.created_at as assigned_at', 'asadby.name as assigned_by','consolidations.consolidation_id','ras.admin_id as assigned_agent_id','tat_options.value as tat_value','u.rcp_tat_option_id as tat_option_id'/*,'rcps.count as message_count'*/)
            ->whereIn('shipments.shipper_status_id', [12,52])
            ->groupBy('shipments.id');
        if(session('department_id') == 7){
            if(!in_array(session('id'), session('sale_users_bypass'))){
                $shipments = $shipments->where(function ($query) {
                    $query->whereIn('u.id', session('tagged_shippers'));
                });
            }
        }
        if (session('role_id') != 1) {
            $shipments = $shipments->whereIn('dc.hub_id', session('hubs'));
            if (in_array(317, session('permissions'))) {
                $shipments = $shipments->where('ras.admin_id', Auth::id());
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
                $route = route('admin.tracking.index');
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
            ->editColumn('shipper_remarks', function ($shipment) {
                if ($shipment->current_status_id == 52) {
                    return $shipment->shipper_remarks;
                }
                else {
                    return '';
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
                return '<button type="button" class="btn btn-sm btn-outline-info align-middle consignee_info_label" rel="'. $shipper->consignee_phone_number_1 .'"><i class="la la-lg la-phone align-middle"></i> <span class="align-middle">' . $consignee_phone . '</span></button>';

            })
            ->filterColumn('shipper_phone',function ($query,$keyword){
                $keyword = strtolower($keyword);
                if ($keyword != '') {
                    $query->where('u.phone', 'like', '%'.$keyword.'%')->orWhere('u.phone2', 'like', '%'.$keyword.'%');
                }

                else {
                    $query->whereRaw('false');
                }
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
            ->orderColumn('shipper_phone', 'u.phone $1, u.phone2 $1')

            ->orderColumn('consignee_phone', 'shipments.consignee_phone_number_1 $1, shipments.consignee_phone_number_2 $1')

            ->addColumn('shipment_remarks',function ($shipments){
                $remark = '<textarea style="width:200px;" placeholder="Enter Remarks" class="form-control form-control-sm" rows="4" cols="100">'.$shipments->remarks.'</textarea>';
                return $remark;
            })
            ->addColumn('reattemp_status_remarks',function ($shipments){
                $reattempt_remarks_col = ReattemptShipmentStatusRemarks::where('shipment_id',$shipments->shId);
                if($reattempt_remarks_col->exists()){
                    $reattempt_remarks_col = $reattempt_remarks_col->orderBy('id', 'desc')->first();
                    return $reattempt_remarks_col->remarks;
                }else{
                    return "-";
                }
            })
            
            ->addColumn('confirmation_on',function ($result){

                $diff_days = self::check_tat($result->last_status_date,$result->tat_value);
                if($result->tat_option_id == 1)
                {
                    return "-";
                }
                else {
                    return $diff_days;
                }
            })
            ->addColumn('confirmation_req',function ($result){
                $diff_days = self::check_tat($result->last_status_date,$result->tat_value);
                if($result->tat_option_id == 1)
                {
                    return "as per shipper";
                }
                else if($diff_days < 1)
                {
                    return "today";
                }
                else{
                    return "hold";
                }
            })
            ->editColumn('status_date',function ($shipments){
                if($shipments->status_date) {
                    if (2 - ((new \Carbon\Carbon($shipments->status_date, 'UTC'))->diffInDays()) < 0) {
                        return "<span class='danger font-weight-bold'>" . $shipments->status_date . "</span>";
                    } else {
                        return Carbon::parse($shipments->status_date)->toDateString();
                    }
                }else{
                    return " - ";
                }
            })
            ->editColumn('arrival',function($shipments){
                if($shipments->arrival){
                    return Carbon::parse($shipments->arrival)->toDateString();
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

			->addColumn('consolidation', function($shipments){
                $consolidations = DeliveryController::check_consolidation($shipments->shId);
                $consol = '';
                if($consolidations){
                    $consol = $consolidations['order'] . '/'. $consolidations['count'];
                }
                else{
                    $consol = '-';
                }
                return $consol;
            })
            ->addColumn('consolidated_id', function ($shipments){
                if($shipments->consolidation_id){
                    return $shipments->consolidation_id;
                }else{
                    return '-';
                }
            })
            ->addColumn('OsaStatus', function ($shipments){//using for checking the nsa shipment to not add checkbox in the datatable
               if($shipments->reason_id == 12){
                    return 1;
                }
                else{
                    return 0;
                }
            })
            ->addColumn("action", function ($result) {
                
                if($result->reason_id == 12)
                    $contains = 1;
                else
                    $contains = 0;
                $open_intercept = CityDelivery::where('city_id', $result->consignee_city_id)->where('shipping_mode_id',$result->shipping_mode_id)->exists();
                $confirm_button = '<a href="javascript:void(0);" class="dropdown-item returnMarkStatus" data-id="'.$contains.'" data-action="confirm"><i class="ft-plus-circle primary"></i> Confirm</a>';//data-id is checking whter it is OSA/NSA or not 1 for yes and 0 for no
                $re_attempt_button = '<a href="javascript:void(0);" class="dropdown-item returnMarkStatus" data-id="'.$contains.'" data-action="reattempt"><i class="ft-plus-circle primary"></i> Re-Attempt</a>';//data-id is checking whter it is OSA/NSA or not 1 for yes and 0 for no
                $intercept = '<a href="javascript:void(0);" class="dropdown-item intercept"><i class="ft-plus-circle primary"></i> Intercept/Re-Book</a>';
                $self_collection_button = '<a href="javascript:void(0);" class="dropdown-item selfCollection" data-action="selfCollection"><i class="ft-plus-circle primary"></i> Mark for Self Collection</a>';
                $edit_estimate_charges = '<a href="javascript:void(0);" class="dropdown-item editEstimateCharges" data-action="editEstimateCharges"><i class="ft-plus-circle primary"></i> Edit Estimate Charges</a>';
                $manual_sms_btn = '<a href="javascript:void(0);" class="dropdown-item rcp_sms"><i class="ft-mail primary"></i> Send SMS</a>';

                $diff_days = self::check_tat($result->last_status_date,$result->tat_value);
                if(session("role_id") == 1 || $result->assigned_agent_id == Auth::id() || $diff_days < 1 || (in_array(490, session('permissions')))) {
                    if (session('role_id') == 1 || count(array_intersect([45, 46, 211, 212, 245], session('permissions'))) !== 0) {
                        $dropdown = "
                        <div class='btn-group'>
                           <button type='button' class='btn btn-sm btn-success dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Actions</button>
                            <div class='dropdown-menu dropdown-menu-sm'>";

                        if ((session('role_id') == 1 || (in_array(45, session('permissions')))) && !$result->consolidation_id) {
                            $dropdown .= $confirm_button;
                        }

                        if ((session('role_id') == 1 || in_array(46, session('permissions'))) && !$result->consolidation_id) {
                            $dropdown .= $re_attempt_button;
                        }

                        if (session('role_id') == 1 || in_array(211, session('permissions'))) {
                            if ($result->reason_id == 12 || $result->current_status_id == 52) {
                                $dropdown .= $self_collection_button;
                            }
                        }

                        if (session('role_id') == 1 || in_array(212, session('permissions'))) {
                            if ($result->reason_id == 12 && ($result->current_status_id == 52 || $result->current_status_id == 12)) {
                                $dropdown .= $edit_estimate_charges;
                            }
                        }
                        if ((session('role_id') == 1 || in_array(245, session('permissions'))) && !$result->consolidation_id) {
                            if (($result->current_status_id == 12 || $result->current_status_id == 52) && $result->journey_shipper_status_id != 53 && $result->intercepted == 0) {
                                if ($open_intercept) {
                                    $dropdown .= $intercept;
                                }
                            }
                        }
                        if(session('role_id') == 1 || in_array(700, session('permissions'))){
                            $dropdown .= $manual_sms_btn;
                        }

                        $dropdown .= "
                            </div>
                        </div>
                    ";

                        return $dropdown;
                    } else {
                        return '';
                    }
                }
                else{
                    return '';
                }
            });
        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $datatable->whereIn('shipments.tracking_number', explode(',', $tracking_numbers));
        }
        if($mode = $request->get('search_shipping_mode')){
            $datatable->where('sm.id', '=', $mode);
        }
        return $datatable->make(true);
    }

    public static function check_tat($last_status_date,$tat_value)
    {
        $temp_status_date = Carbon::parse($last_status_date)->format("Y-m-d 00:00:00");
        $status_date = Carbon::parse($temp_status_date);
        $temp_tat_date = Carbon::parse($temp_status_date)->addDays($tat_value);
        $sundays = $status_date->diffInDaysFiltered(function(Carbon $date) {
            if($date->format('D') == "Sun")
            {
                return $date;
            }
        }, $temp_tat_date->addDay());
        $tat_date = Carbon::parse($temp_status_date)->addDays($tat_value+$sundays);
        $diff_days = now()->diffInDaysFiltered(function(Carbon $date) {
                if($date->format('D') != "Sun")
                {
                    return $date;
                }
            }, $tat_date,false);
        if($diff_days < 0) {
            $diff_days = 0;
        }

        return $diff_days;
    }

    public function return_confirm_status(Request $request){ //update to status 20 for confirm and 13 for re-attempt
        $shipment_ids = $request->shipment_ids;
        $return_reason = $request->return_reason_select;
        $consignee_refused_reasons = $request->consignee_refused_reasons;
        $remarks = $request->remark;

        if($request->action == 'confirm'){

            foreach ($shipment_ids as $shipment){
                $parcel = Shipment::find($shipment);
                $dispute_check = CheckDisputeShipmentsController::check($parcel->id);
                if(!$dispute_check){
                    return ['status' => 0, 'error' => 'Shipment is in Dispute, please resolve dispute first!'];
                }
                if($parcel->booking_type_id == 5){
                    continue;
                }
                $remark_inp = "remark.$shipment";
                if(!in_array($parcel->shipper_status_id, [5, 13, 15, 20, 54, 55])){

                //    $remarks = ($request->has($remark_inp) && $request->remark[$parcel->id] != null)? $request->remark[$parcel->id] : null;
                //    $shipment_history = ShipmentsJourney::where('shipment_id',$shipment)->latest()->first();
                    $parcel->shipper_status_id = 20;
                    $parcel->consignee_status_id = 20;
                    $parcel->save();


                    NotificationsController::send(15, 0, $shipment);
                    NotificationsController::send(16, 0, $shipment);

                    if ($parcel->shipment_type == 1) {
                        if ($parcel->booking_type_id != 4) {
                            ShipmentChargesController::return($shipment);

                            if ($parcel->packaging_material_request != 1) {

                                AdminFinanceController::add_payment($shipment, 1);

                            }
                        }
                        else {
                            ShipmentChargesController::walk_in_return($shipment);

                            $parcel->walk_in_status = 2;

                            $parcel->save();

                            AdminFinanceController::done_payment($shipment, 1);
                        }
                    }
                    ShipmentsJourneyController::add($shipment, 20, 20, $return_reason, $remarks, NULL, Auth::id(),null,null,1,null,null,$consignee_refused_reasons);
                    $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $shipment);
                   if($return_assign_shipment->exists()){

                       $return_assign_shipment = $return_assign_shipment ->latest()->first();
                       $return_assign_shipment->status = 0;
                       $return_assign_shipment->save();

                       $return_assign_log = new ReturnAssignedShipmentLogs();
                       $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                       $return_assign_log->status = 2;
                       $return_assign_log->assigned_by = Auth::id();
                       $return_assign_log->save();
                   }

                }

            }
            return ['status'=>1,'success'=>"Shipment successfully updated as ( Return Confirm )"];
        }
    }
    public function unassign_agent(Request $request){
        $shipment_ids = $request->shipment_ids;

        if($request->action == 'un-assign'){
            foreach ($shipment_ids as $shipment){
                $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $shipment)->where('status', 1);
                if($return_assign_shipment->exists()){
                    $return_assign_shipment = $return_assign_shipment->latest()->first();
                    $return_assign_shipment->status = 0;
                    $return_assign_shipment->save();


                    $return_assign_log = new ReturnAssignedShipmentLogs();
                    $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                    $return_assign_log->status = 4;
                    $return_assign_log->assigned_by = Auth::id();
                    $return_assign_log->save();
                }
            }
            return ['status'=>1,'success'=>"Agent Unassigned successfully"];

        }
    }
    
    public function return_reattempt_status(Request $request){ //update to status 20 for confirm and 13 for re-attempt
        $shipment_ids = $request->shipment_ids;

        if($request->action == 'reattempt'){
            foreach ($shipment_ids as $shipment){
                $parcel = Shipment::find($shipment);
                if(!in_array($parcel->shipper_status_id, [13, 20])){
                    $remark_inp = "remark.$shipment";
                    $remarks = ($request->has($remark_inp) && $request->remark[$parcel->id] != null)? $request->remark[$parcel->id] : null;

                    $journey = ShipmentsJourney::where('shipment_id', $shipment)->where('shipper_status_id', 12)->latest('id')->first();



                    if ($journey) {
                        if ($parcel->shipper_status_id == 12 && ($journey->status_reason_id == 12)) {
                            $parcel->nsa_osa_status = 1;

                            $parcel->save();

                            ShipmentChargesController::nsa_osa_charges($shipment);

                            NotificationsController::send(33, $shipment);
                        }
                        else if ($parcel->shipper_status_id == 52) {
                            $journey = ShipmentsJourney::where('shipment_id', $shipment)->where('shipper_status_id', 12)->latest('id')->first();

                            if ($journey && ($journey->status_reason_id == 12)) {
                                $parcel->nsa_osa_status = 1;

                                $parcel->save();

                                ShipmentChargesController::nsa_osa_charges($shipment);
                            }
                        }
                    }

                    $parcel->shipper_status_id = 13;
                    $parcel->consignee_status_id = 13;
                    $parcel->save();

                    ShipmentsJourneyController::add($shipment, 13, 13, NULL, $remarks, NULL, Auth::id());
                   $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $shipment)->latest()->first();
                   if($return_assign_shipment){
                       $return_assign_shipment->status = 0;
                       $return_assign_shipment->save();

                       $return_assign_log = new ReturnAssignedShipmentLogs();
                       $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                       $return_assign_log->status = 1;
                       $return_assign_log->assigned_by = Auth::id();
                       $return_assign_log->save();
                   }
                    NotificationsController::send(15, 0, $shipment);
                    NotificationsController::send(16, 0, $shipment);

                    $reattempt_remarks_col = new ReattemptShipmentStatusRemarks;
                    $reattempt_remarks_col->shipment_id = $shipment;
                    $reattempt_remarks_col->remarks = 'Manual';
                    $reattempt_remarks_col->save();
                }

            }
            return ['status'=>1,'success'=>"Shipment successfully updated as ( Re-Attempt )"];

        }
    }

    public function return_marked_single_status(Request $request){
        $remark = $request->remark;
        if($request->action == 'confirm'){
            $return_reason = $request->single_return_reason_select;
            $single_consignee_refused_reasons = $request->single_consignee_refused_reasons;
            $consignee_refused_reasons = $request->consignee_refused_reasons;
            $parcel = Shipment::find($request->shipment_id);
            // $pickup_address_city = $parcel->pickup_address->city_id;
            // if(!in_array($pickup_address_city, session('hubs')))
            // {
            //     return ['status' => 0,'error' => "Shipments is not from your assigned Hub"];
            // }
            $dispute_check = CheckDisputeShipmentsController::check($parcel->id);
            if(!$dispute_check){
                return ['status' => 0, 'error' => 'Shipment is in Dispute, please resolve dispute first!'];
            }
            if($parcel->booking_type_id == 5){
                return ['status' => 0,'error' => "Reverse Pickup Shipment can not be updated to Return Confirm!"];
            }
            if(!in_array($parcel->shipper_status_id, [13, 15, 20, 54, 55]) && ($parcel->shipper_status_id == 12 || $parcel->shipper_status_id == 52)){

                Shipment::where('id',$request->shipment_id)->update(['shipper_status_id'=>20,'consignee_status_id'=>20]);



                NotificationsController::send(15, 0, $request->shipment_id);
                NotificationsController::send(16, 0, $request->shipment_id);

                if ($parcel->shipment_type == 1) {
                    if ($parcel->booking_type_id != 4) {
                        ShipmentChargesController::return($request->shipment_id);

                        if ($parcel->packaging_material_request != 1) {
                            AdminFinanceController::add_payment($request->shipment_id, 1);
                        }
                    }
                    else {
                        ShipmentChargesController::walk_in_return($request->shipment_id);

                        $parcel->walk_in_status = 2;

                        $parcel->save();

                        AdminFinanceController::done_payment($request->shipment_id, 1);
                    }
                }
                ShipmentsJourneyController::add($request->shipment_id, 20, 20, $return_reason, $remark, NULL, Auth::id(),null,null,1,null,null,$consignee_refused_reasons);
                $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $request->shipment_id);
                if($return_assign_shipment->exists()){
                   $return_assign_shipment = $return_assign_shipment ->latest()->first();
                   $return_assign_shipment->status = 0;
                   $return_assign_shipment->save();

                   $return_assign_log = new ReturnAssignedShipmentLogs();
                       $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                       $return_assign_log->status = 2;
                       $return_assign_log->assigned_by = Auth::id();
                       $return_assign_log->save();
               }

                return ['status'=>1,'success'=>"Shipment successfully marked as Shipment - Return Confirm"];
            }
            return ['status'=>0,'error'=>"Shipment is in different status, Cannot mark it as Return - Confirm!"];


        }else if($request->action == 'reattempt'){
            $parcel = Shipment::find($request->shipment_id);
            if($request->has('charges'))
            {
                if($request->charges != null){
                    $check= $this->update_estimatecharges($request->shipment_id, $request->charges);
                    if($check != 0)
                    {
                        return ['status'=>0,'error'=>"Shipment not found on Estimation Charges"];
                    }
                }
                else{
                    
                }
            }
            // $pickup_address_city = $parcel->pickup_address->city_id;
            // if(!in_array($pickup_address_city, session('hubs')))
            // {
            //     return ['status' => 0,'error' => "Shipments is not from your assigned Hub"];
            // }
            if(!in_array($parcel->shipper_status_id, [13, 20]) && ($parcel->shipper_status_id == 12 || $parcel->shipper_status_id == 52)){
                $journey = ShipmentsJourney::where('shipment_id', $request->shipment_id)->whereIn('shipper_status_id', [12, 52])->latest('id')->first();

                if ($journey) {
                    if ($parcel->shipper_status_id == 12 && ($journey->status_reason_id == 12)) {
                        $parcel->nsa_osa_status = 1;

                        $parcel->save();

                        ShipmentChargesController::nsa_osa_charges($request->shipment_id);

                        NotificationsController::send(33, $request->shipment_id);
                    }
                    else if ($parcel->shipper_status_id == 52) {
                        $journey = ShipmentsJourney::where('shipment_id', $request->shipment_id)->where('shipper_status_id', 12)->latest('id')->first();

                        if ($journey && ($journey->status_reason_id == 12)) {
                            $parcel->nsa_osa_status = 1;

                            $parcel->save();

                            ShipmentChargesController::nsa_osa_charges($request->shipment_id);
                        }
                    }

                    $parcel->shipper_status_id = 13;
                    $parcel->consignee_status_id = 13;
                    $parcel->save();

                    ShipmentsJourneyController::add($request->shipment_id, 13, 13, NULL, $remark, NULL, Auth::id());

                   $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $request->shipment_id)->latest()->first();
                   if($return_assign_shipment){
                       $return_assign_shipment->status = 0;
                       $return_assign_shipment->save();

                       $return_assign_log = new ReturnAssignedShipmentLogs();
                       $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                       $return_assign_log->status = 1;
                       $return_assign_log->assigned_by = Auth::id();
                       $return_assign_log->save();
                   }

                    NotificationsController::send(15, 0, $request->shipment_id);
                    NotificationsController::send(16, 0, $request->shipment_id);
                    $reattempt_remarks_col = new ReattemptShipmentStatusRemarks;
                    $reattempt_remarks_col->shipment_id = $request->shipment_id;
                    $reattempt_remarks_col->remarks = 'Manual';
                    $reattempt_remarks_col->save();
                }

                return ['status'=>1,'success'=>"Shipment successfully marked as Shipment - Re-Attempt"];
            }
            return ['status'=>0,'error'=>"Shipment is in different status, Cannot mark it as Reattempted!"];
        }else{
            return ['status'=>0,'error'=>"Invalid action, Please refresh your page!"];
        }

    }

    public function change_status_to_self_collection(Request $request){
        $shipmentId = $request->shipment_id;
        $remark = $request->remark;
        if($shipmentId){
            if(Shipment::where('id', $shipmentId)->where('shipper_status_id','!=', 15)->exists()){
               $consolidated_shipments = ConsolidationShipments::where('shipment_id', $shipmentId);
                if($consolidated_shipments->exists()){
                    $consolidated_shipments = $consolidated_shipments->first();
                    $all_consolidation_shipments = ConsolidationShipments::where('consolidation_id', $consolidated_shipments->consolidation_id)->pluck('shipment_id')->toArray();
                    Shipment::whereIn('id',$all_consolidation_shipments)->update(['shipper_status_id'=>15,'consignee_status_id'=>15]);
                    foreach ($all_consolidation_shipments as $shipment){
                        ShipmentsJourneyController::add($shipment, 15, 15, NULL, $remark, NULL, Auth::id());
                        $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $shipment)->latest()->first();
                       if($return_assign_shipment){
                           $return_assign_shipment->status = 0;
                           $return_assign_shipment->save();
                       
                           $return_assign_log = new ReturnAssignedShipmentLogs();
                            $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                            $return_assign_log->status = 7;
                            $return_assign_log->assigned_by = Auth::id();
                            $return_assign_log->save();
                        }

                    }
                }else{
                    Shipment::where('id',$request->shipment_id)->update(['shipper_status_id'=>15,'consignee_status_id'=>15]);
                    ShipmentsJourneyController::add($request->shipment_id, 15, 15, NULL, $remark, NULL, Auth::id());
                    $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $request->shipment_id)->latest()->first();
                       if($return_assign_shipment){
                           $return_assign_shipment->status = 0;
                           $return_assign_shipment->save();
                       
                           $return_assign_log = new ReturnAssignedShipmentLogs();
                            $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                            $return_assign_log->status = 7;
                            $return_assign_log->assigned_by = Auth::id();
                            $return_assign_log->save();
                        }
                }

                return ['status'=>0, 'success'=>"Shipment status successfully updated to Shipment - On Hold for Self Collection"];
            }else{
                return response()->json(['status' => 1, 'error' => 'Shipment already updated to Shipment - On Hold for Self Collection!']);
            }

        }else{
            return response()->json(['status' => 1, 'error' => 'Shipment ID Not selected!']);
        }
    }

    public function update_estimated_charges(Request $request){
        $shipment_id = $request->shipment_id;
        $charges = $request->charges;
        if($shipment_id){
            if($charges != null){
                $consolidated_shipments = ConsolidationShipments::where('shipment_id', $shipment_id);
                if($consolidated_shipments->exists()) {
                    $consolidated_shipments = $consolidated_shipments->first();
                    $all_consolidation_shipments = ConsolidationShipments::where('consolidation_id', $consolidated_shipments->consolidation_id)->pluck('shipment_id')->toArray();
                    Shipment::whereIn('id', $all_consolidation_shipments)->update(['nsa_osa_estimated_charges' => $charges]);
                }else{
                    $shipment = Shipment::find($shipment_id);
                    $shipment->nsa_osa_estimated_charges = $charges;
                    $shipment->save();
                }
                $this->add_osa_charges($shipment_id,$charges);
                return response()->json(['status' => 0, 'success' => 'Charges Updated!']);
            }else{
                return response()->json(['status' => 1, 'error' => 'Charges not entered!']);
            }

        }else{
            return response()->json(['status' => 1, 'error' => 'Shipment Not found!']);
        }
    }

    public function excel_store_revert(Request $request){
        $names = [
            'tracking_number' => 'Tracking Number',
            'shipper_status_id' => 'Status (0 - Revert)',
            'remarks' => 'Remarks'
        ];
        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
            'digits_between' => ':attribute must be between :min and :max Digits.',
            'unique' => ':attribute is already Present.'
        ];
        $rules = [
            'tracking_number' => ['required', 'integer'],
            'shipper_status_id' => ['required', 'integer', 'digits_between:0,1'],
            'remarks' => ['nullable', 'between:0,190']
        ];
        $fields = [0 => 'tracking_number', 1 => 'shipper_status_id', 2 => 'remarks'];

        if ($file = $request->file('shipments')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Tracking Number', 'Status (0 - Revert)', 'Remarks'];
        }
        if (isset($spreadsheet)) {
            $header_correct = TRUE;

            foreach ($spreadsheet[0] as $index => $header_value) {
                if ($index == 2) {
                } elseif (!isset($header[$index]) || $header_value != $header[$index]) {
                    $header_correct = FALSE;
                    break;
                }
            }

            if (!$header_correct) {
                return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
            } else {
                unset($spreadsheet[0]);
            }
        }
        if (!empty($spreadsheet) || !isset($spreadsheet)) {
            $rows = array();
            foreach ($spreadsheet as $spreadsheet_row) {
                $row = array();

                foreach ($spreadsheet_row as $key => $value) {
                    $row[$fields[$key]] = $value;
                }

                $rows[] = $row;
            }

            unset($spreadsheet);
            $errors = array();
            $tracking_ids = array();
            $tracking_id_row = array();
            foreach ($rows as $key => $row) {
                $row_id = $key + 2;

                $validate = Validator::make($row, $rules, $messages);

                $validate->setAttributeNames($names);

                if ($validate->fails()) {
                    $errors['Row #' . $row_id] = $validate->errors()->all();
                }
                if (empty($errors['Row #' . $row_id])) {
                    if (!empty(trim($row['tracking_number']))) {
                        if (empty($tracking_ids)) {
                            $tracking_ids[] = $row['tracking_number'];
                            $tracking_id_row[$row['tracking_number']] = $row_id;
                        } else {
                            if (in_array($row['tracking_number'], $tracking_ids)) {
                                $errors['Row #' . $row_id][] = 'Same Tracking Number as of Row #' . $tracking_id_row[$row['tracking_number']];
                            } else {
                                $tracking_ids[] = $row['tracking_number'];
                                $tracking_id_row[$row['tracking_number']] = $row_id;
                            }
                        }
                    }
                    if (!Shipment::where('tracking_number', $row['tracking_number'])->where('shipper_status_id', 20)->exists()) {
                        $errors['Row #' . $row_id][] = 'Supplied Tracking Number is invalid #' . $row['tracking_number'];
                    }
                }


            }
            if (empty($errors)) {
                $tracking_numbers = array();
                foreach ($rows as $key => $row) {
                    $row_id = $key + 2;
                    $tracking = trim($row['tracking_number']);
                    $status = trim($row['shipper_status_id']);
                    $remarks = trim($row['remarks']);

                    if (!empty($row['remarks'])) {
                        $remarks = trim($row['remarks']);
                    } else {
                        $remarks = NULL;
                    }
                    $shipment = Shipment::where('tracking_number', $tracking)->first();
                    // $shipment_history = ShipmentsJourney::where('shipment_id',$shipment_details->id)->latest()->first();
                    if ($status == 0) {
                        $flag = true;
                        $consolidation = ConsolidationShipments::where('shipment_id', $shipment->id)->first();
                        if ($consolidation) {
                            $consolidation_shipments = ConsolidationShipments::where('consolidation_id', $consolidation->consolidation_id)->get();
                            foreach ($consolidation_shipments as $consolidation_shipment) {
                                $is_shipment = Shipment::find($consolidation_shipment->shipment_id);
                                if ($is_shipment->shipper_status_id == 23) {
                                    $flag = false;
                                }
                            }
                        }
                        if ($flag == true) {
                            if ($shipment->shipper_status_id == 20) {
                                if ($consolidation) {
                                    $consolidation_shipments = ConsolidationShipments::where('consolidation_id', $consolidation->consolidation_id)->get();
                                    foreach ($consolidation_shipments as $consolidation_shipment) {
                                        $is_shipment = Shipment::find($consolidation_shipment->shipment_id);

                                        $is_shipment->shipper_status_id = 13;
                                        $is_shipment->consignee_status_id = 13;

                                        $is_shipment->save();
                                        $is_journey = ShipmentsJourney::where('shipment_id', $is_shipment->id)->where('shipper_status_id', 20)->latest()->first();
                                        if ($is_journey) {
                                            $return_reattempt = new ReturnReattemptRatio();
                                            $return_reattempt->shipment_id = $is_shipment->id;
                                            $return_reattempt->return_confirm_date = $is_journey->created_at;
                                            $return_reattempt->save();
                                        }

                                        ShipmentsJourneyController::add($is_shipment->id, 13, 13, NULL, $remarks, NULL, Auth::id());
                                        if ($is_shipment->shipment_type == 1) {
                                            AdminFinanceController::return_confirmed_revert($is_shipment->id, 1);
                                        }
                                    }
                                } else {
                                    $shipment->shipper_status_id = 13;
                                    $shipment->consignee_status_id = 13;

                                    $shipment->save();

                                    $journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 20)->latest()->first();
                                    if ($journey) {
                                        $return_reattempt = new ReturnReattemptRatio();
                                        $return_reattempt->shipment_id = $shipment->id;
                                        $return_reattempt->return_confirm_date = $journey->created_at;
                                        $return_reattempt->save();
                                    }


                                    ShipmentsJourneyController::add($shipment->id, 13, 13, NULL, $remarks, NULL, Auth::id());
                                    if ($shipment->shipment_type == 1) {
                                        AdminFinanceController::return_confirmed_revert($shipment->id, 1);
                                    }
                                }
                            }
                            $shipment->save();
                            $tracking_numbers['Row #' . $row_id] = $tracking;
                        }
                    }
                }
                $tracking_numbers = implode(' | ', array_map(function ($row, $tracking_number) {
                    return $row . ': ' . $tracking_number;
                }, array_keys($tracking_numbers), $tracking_numbers));

                return redirect()->back()->with(['success' => 'Total ' . count($rows) . ' Shipment(s) Reverted with Tracking Number(s):' . PHP_EOL . $tracking_numbers]);
            } else {
                $errors = array_map(function ($row, $errors) {
                    return $row . ':' . PHP_EOL . implode(' | ', $errors);
                }, array_keys($errors), $errors);

                return redirect()->back()->withErrors($errors);
            }

        } else {
            return redirect()->back()->with('error', 'No Shipments in File');
        }
    }

    public function excel_store(Request $request){
        $names = [
            'tracking_number' => 'Tracking Number',
            'shipper_status_id' => 'Status (0 - Confirm / 1 - Re-Attempt)',
            'remarks' => 'Remarks',
            'estimation_charges' => 'Estimation Charges'
        ];
        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
            'digits_between' => ':attribute must be between :min and :max Digits.',
            'unique' => ':attribute is already Present.'
        ];
        $rules = [
            'tracking_number' => ['required', 'integer'],
            'shipper_status_id' => ['required', 'integer', 'digits_between:0,1'],
            'remarks' => ['nullable', 'between:0,190'],
            'estimation_charges' => ['nullable', 'integer']
        ];
        $fields = [0 => 'tracking_number', 1 => 'shipper_status_id', 2 => 'remarks', 3 => 'estimation_charges'];

        if($file = $request->file('shipments')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Tracking Number', 'Status (0 - Confirm / 1 - Re-Attempt)', 'Remarks', 'Estimation Charges'];
        }
        if (isset($spreadsheet)) {
            $header_correct = TRUE;

            foreach ($spreadsheet[0] as $index => $header_value) {
                if($index == 2){
                }
                elseif (!isset($header[$index]) || $header_value != $header[$index]) {
                    $header_correct = FALSE;
                    break;
                }
            }

            if (!$header_correct) {
                return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
            }
            else {
                unset($spreadsheet[0]);
            }
        }
        if (!empty($spreadsheet) || !isset($spreadsheet)) {
            $rows = array();
            foreach ($spreadsheet as $spreadsheet_row) {
                $row = array();

                foreach ($spreadsheet_row as $key => $value) {
                    $row[$fields[$key]] = $value;
                }

                $rows[] = $row;
            }

            unset($spreadsheet);
            $errors = array();
            $tracking_ids = array();
            $tracking_id_row = array();
            foreach ($rows as $key => $row) {
                $row_id = $key + 2;

                $validate = Validator::make($row, $rules, $messages);

                $validate->setAttributeNames($names);

                if ($validate->fails()) {
                    $errors['Row #' . $row_id] = $validate->errors()->all();
                }
                if (empty($errors['Row #' . $row_id])) {
                    $shid = Shipment::where('tracking_number',$row['tracking_number'])->first();
                    $parcel = ShipmentsJourney::where('shipment_id',$shid->id)->latest('id')->first();
                    $contains = 0;
                    if($parcel)
                    {
                        if(($parcel->status_reason_id == 12 && $row['shipper_status_id'] == 1 && !is_null($row['estimation_charges'])) || (($row['shipper_status_id'] == 0) && is_null($row['estimation_charges']))){
                            $contains = 1;
                        }
                        else{
                            if(is_null($row['estimation_charges']))
                                $contains = 2;
                            if((($row['shipper_status_id'] == 1) && is_null($row['estimation_charges']) && $parcel->status_reason_id != 12))
                            {
                                $contains = 1;
                            }
                            else
                                $contains = 0;
                        }
                    }
                    if (!empty(trim($row['tracking_number']))) {
                        if (empty($tracking_ids)) {
                            $tracking_ids[] = $row['tracking_number'];
                            $tracking_id_row[$row['tracking_number']] = $row_id;
                        }
                        else {
                            if (in_array($row['tracking_number'], $tracking_ids)) {
                                $errors['Row #' . $row_id][] = 'Same Tracking Number as of Row #' . $tracking_id_row[$row['tracking_number']];
                            }
                            else {
                                $tracking_ids[] = $row['tracking_number'];
                                $tracking_id_row[$row['tracking_number']] = $row_id;
                            }
                        }
                    }
                    if (!Shipment::where('tracking_number', $row['tracking_number'])->whereIn('shipper_status_id', [12, 52])->exists()) {
                        $errors['Row #' . $row_id][] = 'Shipment is not ready for confirmation pending #' . $row['tracking_number'];
                    }
                    if($contains == 0 || $contains == 2)
                    if ($contains == 0) {
                        $errors['Row #' . $row_id][] = 'Shipment is not OSA #' . $row['tracking_number'];
                    }
                    else if ($contains == 2) {
                        $errors['Row #' . $row_id][] = 'OSA Shipment required estimation charges #' . $row['tracking_number'];
                    }
                }


            }
            if(empty($errors)){
                $tracking_numbers = array();
                foreach ($rows as $key => $row) {
                    $row_id = $key + 2;
                    $tracking = trim($row['tracking_number']);
                    $status = trim($row['shipper_status_id']);
                    $remarks = trim($row['remarks']);
                    $estimation_charges = trim($row['estimation_charges']);

                    if (!empty($row['remarks'])) {
                        $remarks = trim($row['remarks']);
                    }
                    else {
                        $remarks = NULL;
                    }
                    $shipment_details = Shipment::where('tracking_number',$tracking)->first();
                    $shipment_history = ShipmentsJourney::where('shipment_id',$shipment_details->id)->latest('id')->first();
                    if($status == 0){
                        if($shipment_details->booking_type_id == 5){
                            continue;
                        }
                        $shipment_details->shipper_status_id = 20; //Confirmation Pending
                        NotificationsController::send(15, 0, $shipment_details->id);
                        NotificationsController::send(16, 0, $shipment_details->id);

                        if ($shipment_details->shipment_type == 1) {
                            if ($shipment_details->booking_type_id != 4) {
                                ShipmentChargesController::return($shipment_details->id);

                                if ($shipment_details->packaging_material_request != 1) {

                                    AdminFinanceController::add_payment($shipment_details->id, 1);

                                }
                            }
                            else {
                                ShipmentChargesController::walk_in_return($shipment_details->id);

                                $shipment_details->walk_in_status = 2;

                                AdminFinanceController::done_payment($shipment_details->id, 1);
                            }
                        }

                        ShipmentsJourneyController::add($shipment_details->id, 20, 20, $shipment_history->status_reason_id, $remarks, NULL, Auth::id());
                    }
                    else if($status == 1){
                        $journey = ShipmentsJourney::where('shipment_id', $shipment_details->id)->where('shipper_status_id', 12)->latest('id')->first();
                        if($journey){
                            if ($shipment_details->shipper_status_id == 12 && ($journey->status_reason_id == 12)) {
                                $shipment_details->nsa_osa_status = 1;
                                $shipment_details->save();
                                ShipmentChargesController::nsa_osa_charges($shipment_details->id);

                                $check= $this->update_estimatecharges($shipment_details->id, $estimation_charges);
                                
                                NotificationsController::send(33, $shipment_details->id);
                            }
                            else if ($shipment_details->shipper_status_id == 52) {
                                $journey = ShipmentsJourney::where('shipment_id', $shipment_details->id)->where('shipper_status_id', 12)->latest('id')->first();

                                if ($journey && ($journey->status_reason_id == 12)) {
                                    $shipment_details->nsa_osa_status = 1;

                                    $shipment_details->save();

                                    ShipmentChargesController::nsa_osa_charges($shipment_details->id);
                                }
                            }
                        }
                        $shipment_details->shipper_status_id = 13; //Re-Attempt
                        $shipment_details->consignee_status_id = 13;
                        ShipmentsJourneyController::add($shipment_details->id, 13, 13, $shipment_history->status_reason_id, $remarks, NULL, Auth::id());

                       $return_assign_shipment = ReturnAssignedShipments::where('shipment_id', $shipment_details->id)->latest()->first();
                       if($return_assign_shipment){
                           $return_assign_shipment->status = 0;
                           $return_assign_shipment->save();
                       
                           $return_assign_log = new ReturnAssignedShipmentLogs();
                            $return_assign_log->return_assign_shipment_id = $return_assign_shipment->id;
                            $return_assign_log->status = 1;
                            $return_assign_log->assigned_by = Auth::id();
                            $return_assign_log->save();
                        }
                        NotificationsController::send(15, 0, $shipment_details->id);
                        NotificationsController::send(16, 0, $shipment_details->id);

                    }
                    $shipment_details->save();
                    $tracking_numbers['Row #' . $row_id] = $tracking;

                }
                $tracking_numbers = implode(' | ', array_map(function ($row, $tracking_number) {
                    return $row . ': ' . $tracking_number;
                }, array_keys($tracking_numbers), $tracking_numbers));

                return redirect()->back()->with(['success' => 'Total ' . count($rows) . ' Shipment(s) Updated with Tracking Number(s):' . PHP_EOL . $tracking_numbers]);
            }
            else{
                $errors = array_map(function ($row, $errors) {
                    return $row . ':' . PHP_EOL . implode(' | ', $errors);
                }, array_keys($errors), $errors);

                return redirect()->back()->withErrors($errors);
            }

        }
        else {
            return redirect()->back()->with('error', 'No Shipments in File');
        }
    }

    public function return_confirmed_view(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),27);
        $shipment_status = ShipmentStatus::select('id','name')->get();
        $shipping_mode = ShippingMode::all();
        $service_type = BookingType::all();
        return view('admin.return.confirmed')->with(['shipment_status'=>$shipment_status,'shipping_mode'=>$shipping_mode,'service_type'=>$service_type]);
    }

    public function return_confirmed_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),87);
        }

        $status_return = array(20,22,24,27,29,30,33,35,37,44,45,46,47,48);
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->leftjoin('user_shipping_infos AS rsi', 'shipments.return_address_id', '=', 'rsi.id')
            ->leftjoin('cities AS rc', 'rsi.city_id', '=', 'rc.id')
            ->join('shipping_modes as sm','sm.id','=','shipments.shipping_mode_id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftJoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where('shipments_journey.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id order by shipments_journey.created_at desc limit 1)'));
            })
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->leftjoin('crm_requests as crm', function ($join) {
                $join->on('crm.shipment_id', '=', 'shipments.id')
                    ->whereIn('crm.status_id', [2, 3, 5])
                    ->where('crm.case_nature_id', 1);
            })
            ->leftJoin('shipment_status_reason as ssr','ssr.id','=','shipments_journey.status_reason_id')
            ->leftjoin('admins as cb', function ($join) {
                $join->on('cb.id', '=', 'shipments_journey.admin_id')
                    ->where('shipments_journey.shipper_status_id', 20);
            })
            ->select('shipments.id as shipment_id','shipments.id as shId', 'shipments.shipper_status_id', 'shipments.tracking_number as tracking_number', 'shipments.tracking_number as tracking','u.name as shipper', 'oc.hub_id as origin_hub_id', 'oc.name as origin', 'dc.hub_id as destination_hub_id', 'dc.name as destination','shipments.order_id','h.name as hub','shipments.consignee_name','shipments.consignee_phone_number_1 as phone','shipments.consignee_address','shipments.amount','sm.mode','bt.booking_type as service_type','ss.name as status','ssr.name as reason','shipments_journey.remarks as remarks','shipments_journey.created_at as status_date','shipments_journey.created_at as last_status_date','sj.created_at as arrival', 'shipments.booking_type_id', 'usi.poc','crm.id as complaint','cb.name as return_confirmed_by','shipments_journey.user_id as shipper_id', 'rc.name as return_city_name', DB::raw('(select count(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 5) as total_attempt'))
            ->whereIn('shipments.shipper_status_id',$status_return);
        if(session('department_id') == 7){
            if(!in_array(session('id'), session('sale_users_bypass')) ){
                $shipments = $shipments->where(function ($query) {
                    $query->whereIn('u.id', session('tagged_shippers'));
                });
            }
        }

        if (session('role_id') != 1) {
            $shipments = $shipments->where(function($query) {
                $query->where(function ($sub_query){
                    $sub_query->whereIn('dc.hub_id', session('hubs'));
                })
                    ->orWhere(function ($sub_query){
                        $sub_query->whereIn('oc.hub_id', session('hubs'));
                    });
            });
        }

        $datatables = Datatables::of($shipments)
            ->setRowAttr([
                'class' => function ($shipments) {
                    if ($shipments->complaint != null) {
                        return 'complaint_row';
                    }else if($shipments->booking_type_id == 3){
                        return "tnb_row";
                    }
                },
            ])
            ->addColumn('return_pending_for', function ($shipment) {
                if (in_array($shipment->shipper_status_id, [22, 24, 27, 29, 33, 35, 44, 45, 46,47,48])) {
                    return 'Shipper';
                }
                else {
                    if ($shipment->origin_hub_id == $shipment->destination_hub_id) {
                        return 'Shipper';
                    }
                    else {
                        return 'Cargo';
                    }
                }
            })
            ->addColumn('return_city', function ($shipment) {
                if($shipment->return_city_name){
                    return $shipment->return_city_name;
                }
                else{
                    return $shipment->origin;
                }
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->editColumn('tracking_number',function ($shipments){
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper .' (' . $shipment->poc . ')';
                }
                else {
                    return $shipment->shipper;
                }
            })
            ->editColumn('return_confirmed_by',function($shipment){
                if($shipment->return_confirmed_by == null && $shipment->shipper_id != null){
                    return 'Shipper';
                }
                else if($shipment->return_confirmed_by != null && $shipment->shipper_id == null ){
                    return  $shipment->return_confirmed_by;
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
            ->editColumn('arrival',function($shipments){
                if($shipments->arrival){
                    return $shipments->arrival;
                }else{
                    return " - ";
                }
            })
            ->addColumn('shipment_remarks',function ($shipments){
                $remark = '<input class="form-control form-control-sm" placeholder="Remarks here.." value="'.$shipments->remarks.'" />';
                return $remark;
            })
            ->filterColumn('status',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('ss.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('return_pending_for', function ($query, $keyword) {
                $keyword = strtolower($keyword);

                if (strpos('shipper', $keyword) !== FALSE) {
                    $query->whereIn('shipments.shipper_status_id', [22, 24, 27, 29, 33, 35, 44, 45, 46,47,48])
                        ->orWhereRaw('`oc`.`hub_id` = `dc`.`hub_id`');
                }
                else if (strpos('cargo', $keyword) !== FALSE) {
                    $query->whereIn('shipments.shipper_status_id', [20, 30, 37])
                        ->whereRaw('`oc`.`hub_id` != `dc`.`hub_id`');
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('action', function($shipment) {
                if (($shipment->shipper_status_id == 20) && (session('role_id') == 1 || in_array(109, session('permissions')))) { //Change ID
					$flag = true;
				        $consolidation = ConsolidationShipments::where('shipment_id', $shipment->shipment_id)->first();
				        if($consolidation){
				            $consolidation_shipments = ConsolidationShipments::where('consolidation_id', $consolidation->consolidation_id)->get();
				            foreach ($consolidation_shipments as $consolidation_shipment){
				                $is_shipment = Shipment::find($consolidation_shipment->shipment_id);
				                if($is_shipment->shipper_status_id == 23){
				                    $flag = false;
				                }
				            }
				        }
				        if($flag == true){
				            $revert_button = '<button type="button" class="dropdown-item revert"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">Revert</div></button>';
				            $dropdown = '
				              <div class="btn-group">
				                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
				                <div class="dropdown-menu dropdown-menu-sm">
				            ';
				                $dropdown .= $revert_button;
				            $dropdown .= '
				                </div>
				              </div>
				            ';
				                return $dropdown;
				        }
				        else{
				            return '';
				        }
				    }
				    else {
				        return '';
				    }
            });
        if ($tracking_numbers = $request->get('tracking_numbers')) {
            $datatables->whereIn('shipments.tracking_number', explode(',', $tracking_numbers));
        }
        if($mode = $request->get('search_shipping_mode')){
            $datatables->where('sm.id', '=', $mode);
        }
        return $datatables->make(true);
    }

    public function return_create_index(){
        $routes = Route::where('status', 1);

        if (session('role_id') != 1) {
            $routes = $routes->whereHas('city', function ($query) {
                $query->whereIn('hub_id', session('hubs'));
            });
        }

        $routes = $routes->get();

        $hubs = City::where([['status',1],['hub',1]]);

        if(session('role_id') != 1)
        {
            $hubs = $hubs->WhereIn('id',session('hubs'));
        }

        $hubs = $hubs->get(['id','name']);

        return view('admin.return.create')->with(['routes'=>$routes,'hubs'=>$hubs]);
    }

    public function get_riders_by_hub(Request $request)
    {
        $hub_id = $request->hub_id;
        return Rider::where('status', 1)
            ->whereHas('city', function ($query) use ($hub_id) {
                $query->where('hub_id', $hub_id);
        })->get(['id','name','route_id','trax_id']);
    }

    public function get_shipment_details(Request $request){
        if($request->tracking != ''){
//            $shipment_not_arrived = array(20,24,27,29,33,35,42,44,45,46);
//            $shipment_arrived = array(22,24,27,29,30,33,35,44,45,46);
            $different_city_statuses_2 = array(22, 24, 27, 29, 33, 35, 42, 44, 45, 46, 47, 48, 60);
            $different_city_statuses = array(22, 24, 27, 29, 33, 35, 37, 42, 44, 45, 46, 47, 48, 60);
            $allowed_statuses = array(20,22,24,27,29,30,33,35,37,42,44,45,46,47,48, 60);
            $return_note_statuses = array(20, 22, 24, 27, 29, 30, 33, 35, 37, 42, 44, 45, 46, 47, 48, 60);
            $shipment = Shipment::where('tracking_number', $request->tracking)->whereIn('shipper_status_id',$allowed_statuses);
            $status = '';
            if($shipment->exists()) {
                $shipment = $shipment->first();
                $dispute_check = CheckDisputeShipmentsController::check($shipment->id);
                if(!$dispute_check){
                    return ['status' => 1, 'error' => 'Shipment is in Dispute, please resolve dispute first!'];
                }
                ShipmentScanningJourneyController::add($shipment->id, 7, 1, Auth::id(), null,null);
                if($request->shipper_id != null){
                    $mandatory_shipper = ReturnReasonMandatoryShipper::pluck('shipper_id')->toArray();
                    if($request->shipper_id != $shipment->user_id){
                        if (in_array($request->shipper_id, $mandatory_shipper) || in_array($shipment->user_id, $mandatory_shipper)){
                            return ['status' => 1, 'error' => 'Different Shipper, scan shipments of same shipper!.'];
                        }
                    }
                }
                if($shipment->return_address_id != NULL){
                    $destination_id = $shipment->return_address->city_id;
                }
                else{
                    $destination_id = $shipment->pickup_address->city_id;
                }
                $destination_id = City::where('id', $destination_id)->select('hub_id')->first();
                $destination_id = $destination_id->hub_id;//first it was origin now for return its destination
                if(session('role_id') == 1 || in_array($destination_id, session('hubs'))){
                    $origin = $shipment->consignee_city->hub_id;//let's suppose consignee city is origin now
                    if(!$request->has('hub_id')){
                        if ($destination_id == $origin && (in_array($shipment->shipper_status_id, $return_note_statuses))) {
                            if($shipment->return_address_id != NULL){
                                $destination_city_id = $shipment->return_address->city_id;
                            }
                            else{
                                $destination_city_id = $shipment->pickup_address->city_id;
                            }

                            $destination_city = City::find($destination_city_id);
                            if ($destination_city->id == $destination_city->hub_id) {
                                $destination = $destination_city->name;
                                $hub = $destination_city->id;
                            } else {
                                $hubid = $destination_city->hub_id;
                                $destinationHub = City::find($hubid);
                                $destination = $destinationHub->name;
                                $hub = $destinationHub->id;
                            }
                            $service = $shipment->booking_type->booking_type;
                            $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id);
                            if ($shipment_journey->exists()) {
                                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->select('shipper_status_id', 'remarks')->latest()->first();
//                            $remarks = ($shipment_journey->remarks != '')? $shipment_journey->remarks:' - ';
                                $status_id = ($shipment_journey->shipper_status_id) ? $shipment_journey->shipper_status_id : '';
                                if ($status_id != '') {
                                    $status_name = ShipmentStatus::where('id', $status_id)->select('name')->first();
                                    $status = $status_name->name;
                                } else {
                                    $status = ' - ';
                                }
                            }
                            if($shipment->booking_type_id != 4) {
                                $settings = GlobalSettings::where('type', 'return_note_restriction_bypass');

                                if ($settings->exists()) {
                                    $settings = $settings->first();
                                    $role_ids = array_map('intval', explode(',', $settings->text));
                                }
                                else {
                                    $role_ids = array();
                                }
                                array_push($role_ids, 1);

                                if (!in_array(session('role_id'), $role_ids)) {
                                    if (!$shipment->packaging_material_request) {
                                        $shipper_payable = 0;
                                        $pending_payment = PendingPayment::where('user_id', $shipment->user_id);
                                        if ($pending_payment->exists()) {
                                            $pending_payment = $pending_payment->first();

                                            $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment->id);
                                            if ($pending_payment_shipments->exists()) {
                                                $pending_payment_shipments = $pending_payment_shipments->get();
                                                foreach ($pending_payment_shipments as $pending_payment_shipment) {
                                                    $shipper_payable += $pending_payment_shipment->payable;
                                                }
                                            }
                                        }
                                        if ($shipper_payable < 0) {
                                            return response()->json(['status' => 1, 'error' => 'Shipper with Negative Balance, Contact Sales Team!']);
                                        }
                                    }
                                }
                            }
                            $class = null;
                            if(CrmRequest::where('shipment_id',$shipment->id)->where('case_nature_id',1)->whereIn('status_id',[2, 3, 5])->exists()){
                                $class = 'complaint_row';
                            }

                            if(!$request->has('pieces_confirm')){
                                if($shipment->booking_type_id == 1 && $shipment->pieces > 1){
                                    $details = array();
                                    $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                                    $details['id'] = $shipment->id;
                                    $details['shipper_id'] = $shipment->user_id;
                                    $details['tracking_number'] = $shipment->tracking_number;
                                    $details['pieces_count'] = $shipment->pieces;
                                    $details['pieces_tracking_numbers'] = $shipment_pieces;
                                    return ['status' => 2, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                                }
                            }
                            return response()->json(['status' => 0, 'shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'destination' => $destination, 'hub' => $hub, 'consignee_name' => $shipment->consignee_name, 'phone' => $shipment->consignee_phone_number_1, 'address' => $shipment->consignee_address, 'amount' => number_format($shipment->amount), 'service_type' => $service, 'shipment_status' => $status, 'class' => $class, 'shipper_id' => $shipment->user_id]);

                        } else
                            if ($destination_id != $origin && (in_array($shipment->shipper_status_id, $different_city_statuses))) {
                                if($shipment->return_address_id != NULL){
                                    $destination_city_id = $shipment->return_address->city_id;
                                }
                                else{
                                    $destination_city_id = $shipment->pickup_address->city_id;
                                }

                                $destination_city = City::find($destination_city_id);
                                if ($destination_city->id == $destination_city->hub_id) {
                                    $destination = $destination_city->name;
                                    $hub = $destination_city->id;
                                } else {
                                    $hubid = $destination_city->hub_id;
                                    $destinationHub = City::find($hubid);
                                    $destination = $destinationHub->name;
                                    $hub = $destinationHub->id;
                                }

                                $service = $shipment->booking_type->booking_type;
                                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id);
                                if ($shipment_journey->exists()) {
                                    $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->select('shipper_status_id', 'remarks')->latest()->first();
//                            $remarks = ($shipment_journey->remarks != '')? $shipment_journey->remarks:' - ';
                                    $status_id = ($shipment_journey->shipper_status_id) ? $shipment_journey->shipper_status_id : '';
                                    if ($status_id != '') {
                                        $status_name = ShipmentStatus::where('id', $status_id)->select('name')->first();
                                        $status = $status_name->name;
                                    } else {
                                        $status = ' - ';
                                    }
                                }
                                if($shipment->booking_type_id != 4) {
                                    $settings = GlobalSettings::where('type', 'return_note_restriction_bypass');

                                    if ($settings->exists()) {
                                        $settings = $settings->first();
                                        $role_ids = array_map('intval', explode(',', $settings->text));
                                        array_push($role_ids, 1);
                                        if (!in_array(session('role_id'), $role_ids)) {
                                            if (!$shipment->packaging_material_request) {
                                                $shipper_payable = 0;
                                                $pending_payment = PendingPayment::where('user_id', $shipment->user_id);
                                                if ($pending_payment->exists()) {
                                                    $pending_payment = $pending_payment->first();

                                                    $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment->id);
                                                    if ($pending_payment_shipments->exists()) {
                                                        $pending_payment_shipments = $pending_payment_shipments->get();
                                                        foreach ($pending_payment_shipments as $pending_payment_shipment) {
                                                            $shipper_payable += $pending_payment_shipment->payable;
                                                        }
                                                    }
                                                }
                                                if ($shipper_payable < 0) {
                                                    return response()->json(['status' => 1, 'error' => 'Shipper with Negative Balance, Contact Sales Team!']);
                                                }
                                            }
                                        }
                                    }
                                }
                                $class = null;
                                if(CrmRequest::where('shipment_id',$shipment->id)->where('case_nature_id',1)->whereIn('status_id',[2, 3, 5])->exists()){
                                    $class = 'complaint_row';
                                }
                                if(!$request->has('pieces_confirm')){
                                    if($shipment->booking_type_id == 1 && $shipment->pieces > 1){
                                        $details = array();
                                        $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                                        $details['id'] = $shipment->id;
                                        $details['shipper_id'] = $shipment->user_id;
                                        $details['tracking_number'] = $shipment->tracking_number;
                                        $details['pieces_count'] = $shipment->pieces;
                                        $details['pieces_tracking_numbers'] = $shipment_pieces;
                                        return ['status' => 2, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                                    }
                                }
                                return response()->json(['status' => 0,'shipper_id' => $shipment->user_id, 'shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'destination' => $destination, 'hub' => $hub, 'consignee_name' => $shipment->consignee_name, 'phone' => $shipment->consignee_phone_number_1, 'address' => $shipment->consignee_address, 'amount' => number_format($shipment->amount), 'service_type' => $service, 'shipment_status' => $status, 'class' => $class]);

                            } else {
                                return ['status' => 1, 'error' => 'Return Shipment not arrived at origin center yet.'];

                            }
                    }else
                        if($request->has('hub_id') && ($destination_id == $request->hub_id)){
                            $same_city_statuses = array(20, 24, 27, 29, 30, 33, 35, 37, 42, 44, 45, 46, 47, 48, 60);
                            if ($destination_id == $origin && (in_array($shipment->shipper_status_id, $same_city_statuses))) {
                                if($shipment->return_address_id != NULL){
                                    $destination_city_id = $shipment->return_address->city_id;
                                }
                                else{
                                    $destination_city_id = $shipment->pickup_address->city_id;
                                }

                                $destination_city = City::find($destination_city_id);
                                if ($destination_city->id == $destination_city->hub_id) {
                                    $destination = $destination_city->name;
                                    $hub = $destination_city->id;
                                } else {
                                    $hubid = $destination_city->hub_id;
                                    $destinationHub = City::find($hubid);
                                    $destination = $destinationHub->name;
                                    $hub = $destinationHub->id;
                                }
                                $service = $shipment->booking_type->booking_type;
                                $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id);
                                if ($shipment_journey->exists()) {
                                    $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->select('shipper_status_id', 'remarks')->latest()->first();
//                            $remarks = ($shipment_journey->remarks != '')? $shipment_journey->remarks:' - ';
                                    $status_id = ($shipment_journey->shipper_status_id) ? $shipment_journey->shipper_status_id : '';
                                    if ($status_id != '') {
                                        $status_name = ShipmentStatus::where('id', $status_id)->select('name')->first();
                                        $status = $status_name->name;
                                    } else {
                                        $status = ' - ';
                                    }
                                }
                                if($shipment->booking_type_id != 4) {
                                    $settings = GlobalSettings::where('type', 'return_note_restriction_bypass');

                                    if ($settings->exists()) {
                                        $settings = $settings->first();
                                        $role_ids = array_map('intval', explode(',', $settings->text));
                                        array_push($role_ids, 1);
                                        if (!in_array(session('role_id'), $role_ids)) {
                                            if (!$shipment->packaging_material_request) {
                                                $shipper_payable = 0;
                                                $pending_payment = PendingPayment::where('user_id', $shipment->user_id);
                                                if ($pending_payment->exists()) {
                                                    $pending_payment = $pending_payment->first();

                                                    $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment->id);
                                                    if ($pending_payment_shipments->exists()) {
                                                        $pending_payment_shipments = $pending_payment_shipments->get();
                                                        foreach ($pending_payment_shipments as $pending_payment_shipment) {
                                                            $shipper_payable += $pending_payment_shipment->payable;
                                                        }
                                                    }
                                                }
                                                if ($shipper_payable < 0) {
                                                    return response()->json(['status' => 1, 'error' => 'Shipper with Negative Balance, Contact Sales Team!']);
                                                }
                                            }
                                        }
                                    }
                                }
                                $class = null;
                                if(CrmRequest::where('shipment_id',$shipment->id)->where('case_nature_id',1)->whereIn('status_id',[2, 3, 5])->exists()){
                                    $class = 'complaint_row';
                                }
                                if(!$request->has('pieces_confirm')){
                                    if($shipment->booking_type_id == 1 && $shipment->pieces > 1){
                                        $details = array();
                                        $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                                        $details['id'] = $shipment->id;
                                        $details['tracking_number'] = $shipment->tracking_number;
                                        $details['pieces_count'] = $shipment->pieces;
                                        $details['pieces_tracking_numbers'] = $shipment_pieces;
                                        return ['status' => 2, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                                    }
                                }
                                return response()->json(['status' => 0, 'shipper_id' =>$shipment->user_id, 'shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'destination' => $destination, 'hub' => $hub, 'consignee_name' => $shipment->consignee_name, 'phone' => $shipment->consignee_phone_number_1, 'address' => $shipment->consignee_address, 'amount' => number_format($shipment->amount), 'service_type' => $service, 'shipment_status' => $status, 'class' => $class]);

                            } else
                                if ($destination_id != $origin && (in_array($shipment->shipper_status_id, $different_city_statuses_2))) {
                                    if($shipment->return_address_id != NULL){
                                        $destination_city_id = $shipment->return_address->city_id;
                                    }
                                    else{
                                        $destination_city_id = $shipment->pickup_address->city_id;
                                    }
                                    $destination_city = City::find($destination_city_id);
                                    if ($destination_city->id == $destination_city->hub_id) {
                                        $destination = $destination_city->name;
                                        $hub = $destination_city->id;
                                    } else {
                                        $hubid = $destination_city->hub_id;
                                        $destinationHub = City::find($hubid);
                                        $destination = $destinationHub->name;
                                        $hub = $destinationHub->id;
                                    }

                                    $service = $shipment->booking_type->booking_type;
                                    $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id);
                                    if ($shipment_journey->exists()) {
                                        $shipment_journey = ShipmentsJourney::where('shipment_id', $shipment->id)->select('shipper_status_id', 'remarks')->latest()->first();
//                            $remarks = ($shipment_journey->remarks != '')? $shipment_journey->remarks:' - ';
                                        $status_id = ($shipment_journey->shipper_status_id) ? $shipment_journey->shipper_status_id : '';
                                        if ($status_id != '') {
                                            $status_name = ShipmentStatus::where('id', $status_id)->select('name')->first();
                                            $status = $status_name->name;
                                        } else {
                                            $status = ' - ';
                                        }
                                    }
                                    if($shipment->booking_type_id != 4) {
                                        $settings = GlobalSettings::where('type', 'return_note_restriction_bypass');

                                        if ($settings->exists()) {
                                            $settings = $settings->first();
                                            $role_ids = array_map('intval', explode(',', $settings->text));
                                            array_push($role_ids, 1);
                                            if (!in_array(session('role_id'), $role_ids)) {
                                                if (!$shipment->packaging_material_request) {
                                                    $shipper_payable = 0;
                                                    $pending_payment = PendingPayment::where('user_id', $shipment->user_id);
                                                    if ($pending_payment->exists()) {
                                                        $pending_payment = $pending_payment->first();

                                                        $pending_payment_shipments = PendingPaymentShipment::where('pending_payment_id', $pending_payment->id);
                                                        if ($pending_payment_shipments->exists()) {
                                                            $pending_payment_shipments = $pending_payment_shipments->get();
                                                            foreach ($pending_payment_shipments as $pending_payment_shipment) {
                                                                $shipper_payable += $pending_payment_shipment->payable;
                                                            }
                                                        }
                                                    }
                                                    if ($shipper_payable < 0) {
                                                        return response()->json(['status' => 1, 'error' => 'Shipper with Negative Balance, Contact Sales Team!']);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    $class = null;
                                    if(CrmRequest::where('shipment_id',$shipment->id)->where('case_nature_id',1)->whereIn('status_id',[2, 3, 5])->exists()){
                                        $class = 'complaint_row';
                                    }
                                    if(!$request->has('pieces_confirm')){
                                        if($shipment->booking_type_id == 1 && $shipment->pieces > 1){
                                            $details = array();
                                            $shipment_pieces = ShipmentPiece::where('shipment_id', $shipment->id)->pluck('tracking_number')->toArray();

                                            $details['id'] = $shipment->id;
                                            $details['tracking_number'] = $shipment->tracking_number;
                                            $details['pieces_count'] = $shipment->pieces;
                                            $details['pieces_tracking_numbers'] = $shipment_pieces;
                                            return ['status' => 2, 'success' => 'Shipment Piece(s) found!', 'details' => $details];
                                        }
                                    }
                                    return response()->json(['status' => 0,'shipper_id'=> $shipment->user_id, 'shId' => $shipment->id, 'tracking_number' => $shipment->tracking_number, 'destination' => $destination, 'hub' => $hub, 'consignee_name' => $shipment->consignee_name, 'phone' => $shipment->consignee_phone_number_1, 'address' => $shipment->consignee_address, 'amount' => ($shipment->amount), 'service_type' => $service, 'shipment_status' => $status, 'class' => $class]);

                                } else {
                                    return ['status' => 1, 'error' => 'Return Shipment not arrived at origin center yet.'];

                                }
                        }else{
                            return ['status' => 1, 'error' => 'Different hub, scan shipments of same hub!.'];
                        }
                } else {
                    return ['status' => 1, 'error' => 'This Shipment doesn\'t belongs to your assigned hubs!'];
                }
            }else{
                return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present, Check tracking!'];
            }


        }
    }

    public function get_piece_details(Request $request)
    {
        $shipment_id = $request->shipment_id;
        $shipment_piece_id = $request->piece_id;

        $shipment_piece = ShipmentPiece::where('tracking_number', $shipment_piece_id);
        if ($shipment_piece->exists()) {
            $shipment_piece = $shipment_piece->first();
            if ($shipment_piece->shipment_id == $shipment_id) {
                $scanned_shipment_piece = $shipment_piece->tracking_number;
                return ['status' => 0, 'success' => 'Shipment Piece found!', 'scanned_shipment_piece' => $scanned_shipment_piece];
            } else {
                return ['status' => 1, 'error' => 'Given Item ID does not belong here'];
            }

        } else {
            return ['status' => 1, 'error' => 'No Shipment Item with given Item ID is present'];
        }
    }

    public function return_note_create(Request $request)
    {
        $trackings = explode(',', $request->shipment_ids);
        $open_box_ids = explode(',',$request->open_box_ids);
        $rider = $request->rider_id;
        $route = $request->route_id;
        $hub_id = $request->hub_id;
        $admin = Auth::id();
        $return_statuses = array(20,22,24,27,29,30,33,35,37,42,44,45,46,47,48, 60);
        $valid_shipments = array();
        $shipments_count = 0;
        if(!empty($trackings)) {
            foreach ($trackings as $shipment_id) {
                $shipment_details = Shipment::find($shipment_id);
                if ($shipment_details) {
                    if (in_array($shipment_details->shipper_status_id, $return_statuses)) {
                        $valid_shipments[] = $shipment_id;
                        $shipments_count++;
                    }
                }
            }
            if ($shipments_count != 0) {

                $note = ReturnNote::create(['hub_id' => $hub_id, 'rider_id' => $rider, 'route_id' => $route, 'shipments_count' => $shipments_count, 'admin_id' => $admin]);

                if ($note) {
                    foreach ($valid_shipments as $index  => $shipment_id) {
                        $shipment = Shipment::where('id', $shipment_id);

                        $shipment = $shipment->first();

                        $old_return_note_id = ReturnNoteShipment::where('shipment_id', $shipment_id)->where('status','=', 1)->orderBy('return_note_id', 'desc');

                        if ($old_return_note_id->exists()) {
                            $old_return_note_id = $old_return_note_id->first();

                            if(ReturnNote::where('id', $old_return_note_id->return_note_id)->where('status',0)->exists()){

                                /*$return_note_shupment = ReturnNoteShipment::where('return_note_id', $old_return_note_id->return_note_id)->where('shipment_id', $shipment->id)->where('status', 0);

                                if($return_note_shupment->exists()){
                                    $return_note_shupment->delete();
                                    unset($valid_shipments);
                                    continue;
                                }*/
                                $journey = ShipmentsJourney::where('shipment_id',$shipment_id)->latest()->first();

                                ShipmentsJourneyController::add($journey->shipment_id,57,NULL,$journey->status_reason_id,$journey->remarks,NULL,Auth::id(),$journey->reference_1_id,NULL,1,NULL);

                            }
                        }
                        if(in_array($shipment_id, $open_box_ids)){
                            $shipment->open_box = 1;
                            ShipmentOpenBoxJourneyController::add($shipment_id, 6,Auth::id());
                        }
                        if(in_array($shipment->booking_type_id, [1,4,5])){
                            ReturnNoteShipment::create(['return_note_id' => $note->id, 'shipment_id' => $shipment_id]);
                            $shipment->shipper_status_id = 23;
                            $shipment->consignee_status_id = 23;
                            $shipment->save();
                            ShipmentsJourneyController::add($shipment->id, 23, 23, NULL, NULL, NULL, Auth::id(), $note->id, $rider);
                        }else{
                            if ($shipment->booking_type_id == 2) {//attempt failed and arrived at origin center

                                ReturnNoteShipment::create(['return_note_id' => $note->id, 'shipment_id' => $shipment_id]);
                                $shipper_status_id = 28;
                                $consignee_status_id = 28;
                                if($shipment->shipper_status_id == 22){
                                    $shipper_status_id = 23;
                                    $consignee_status_id = 23;
                                }
                                $shipment->shipper_status_id = $shipper_status_id;
                                $shipment->consignee_status_id = $consignee_status_id;
                                $shipment->save();
                                ShipmentsJourneyController::add($shipment->id, $shipper_status_id, $consignee_status_id, NULL, NULL, NULL, Auth::id(), $note->id, $rider);


                            } else if ($shipment->booking_type_id == 3) {//attempt failed and arrived at origin center

                                ReturnNoteShipment::create(['return_note_id' => $note->id, 'shipment_id' => $shipment_id]);
                                $shipment->shipper_status_id = 34;
                                $shipment->consignee_status_id = 34;
                                $shipment->save();
                                ShipmentsJourneyController::add($shipment->id, 34, 34, NULL, NULL, NULL, Auth::id(), $note->id, $rider);


                            }else{
                                ReturnNoteShipment::create(['return_note_id' => $note->id, 'shipment_id' => $shipment_id]);
                                $shipment->shipper_status_id = 23;
                                $shipment->consignee_status_id = 23;
                                $shipment->save();
                                ShipmentsJourneyController::add($shipment->id, 23, 23, NULL, NULL, NULL, Auth::id(), $note->id, $rider);
                            }
                        }
                        $return_sheet = ReturnSheet::where('shipment_id', $shipment_id);
                        if($return_sheet->exists()){
                            $return_sheet = $return_sheet->first();
                            $return_sheet->return_note_id = $note->id;
                        }
                        else{
                            $return_sheet = new ReturnSheet();
                            $return_sheet->user_id = $shipment->user_id;
                            $return_sheet->shipment_id = $shipment->id;
                            $return_sheet->return_note_id = $note->id;
                        }
                        $return_sheet->save();

                    }
                    NotificationsController::app_notification(6, $rider, 2, $note->id);
                }
                EmployeeAttendanceController::riders_attendance_mark($rider);

                return redirect()->back()->with(['success' => "Return note has been created with Return Note Number:" . $note->id,'print'=>$note->id]);
            }

        }else{

            return ['error'=>"No shipments scanned"];
        }
    }

    public function return_receive_deliveries_view(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),308);
        return view('admin.return.receive');
    }

    public function return_receive_deliveries_list(Request $request){

        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),309);
        }
        $deliveries = ReturnNote::
        join('cities AS oc', 'return_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'return_notes.rider_id', '=', 'riders.id')
            ->join('admins','admins.id','=','return_notes.admin_id')
            ->select(['return_notes.id as return_note', 'return_notes.id','return_notes.id as return_note_id','oc.name as hub','riders.name as rider','admins.name as assignee','return_notes.created_at','return_notes.shipments_count','return_notes.shipments_count as shipments_count_link','return_notes.status',DB::raw('(SELECT COUNT(shipment_id) FROM return_note_shipments WHERE return_note_id = return_notes.id AND status = 0) AS shipments_unverified_count'), DB::raw('(SELECT COUNT(id) FROM shipments_journey where shipper_status_id in (25, 31, 38) and reference_1_id = return_notes.id and verification = 1 ) as delivered_to_shipper_count')])
            ->whereIn('return_notes.status',[0,3]);

        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('oc.hub_id', session('hubs'));
        }

        $datatables = Datatables::of($deliveries)
            ->editColumn('return_note', function ($deliveries) {
                return "<a href='javascript:void(0);' class='printreturnnote'><u>" . str_pad($deliveries->return_note, 6, '0', STR_PAD_LEFT) . "</u></a>";
            })
            ->addColumn('return_note_id_padded', function ($deliveries) {
                return str_pad($deliveries->return_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->addColumn('return_note_id_padded', function ($deliveries) {
                return str_pad($deliveries->return_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('shipments_count_link', function($deliveries) {
                if ($deliveries->shipments_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->shipments_count . '</button>';
                }
                else {
                    return 0;
                }
            })
            ->editColumn('shipments_unverified_link', function($deliveries) {
                if ($deliveries->shipments_unverified_count != 0) {
                    return  $deliveries->shipments_unverified_count ;
                }
                else {
                    return 0;
                }
            })
            ->filterColumn('return_notes.id', function ($query, $keyword) {
                return $query->where('return_notes.id', '=', $keyword);
            })
            ->addColumn('return_note_status', function ($deliveries){

                if($deliveries->status == 0){
                    return 'Created';
                }else if($deliveries->status == 3){
                    return 'Updated';
                }
            })
            ->addColumn("action", function ($result) {
                $statusUpdate = route('admin.return.receive.status',['id'=>$result->return_note]);
                $route = route('admin.return.receive.update',['id'=>$result->return_note]);

                $receive_button = '<a href="' . $statusUpdate . '" class="dropdown-item"><i class="ft-plus-circle primary"></i> Receive</a>';
                $shift_shipment_button = '<a href="' . $route . '" class="dropdown-item returnnoteupdate"><i class="ft-plus-circle primary"></i> Edit Shipment</a>';
                $return_image_upload = '<a href="javascript:void(0);" class="dropdown-item return_image_upload"><i class="ft-image primary"></i> Image Upload</a>';

                if (session('role_id') == 1 || count(array_intersect([50, 51], session('permissions'))) !== 0) {
                    $dropdown = "
                    <div class='btn-group'>
                           <button type='button' class='btn btn-sm btn-success dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Actions</button>
                            <div class='dropdown-menu dropdown-menu-sm'>";

                    if (session('role_id') == 1 || in_array(50, session('permissions'))) {
                        $dropdown .= $receive_button;
                    }

                    if (($result->created_at->diffInMinutes(Carbon::now()) <= 60) && (session('role_id') == 1 || in_array(51, session('permissions')))) {
                        $dropdown .= $shift_shipment_button;
                    }

                    if($result->status == 3 && $result->delivered_to_shipper_count != 0){
                        $dropdown .= $return_image_upload;
                    }

                    $dropdown .= "
                        </div>
                    </div>
                ";

                    return $dropdown;
                }
                else {
                    return '';
                }
            });

        if ($tracking_number = $request->get('search_tracking')) {
            $datatables->join('return_note_shipments as rns', 'return_notes.id', '=', 'rns.return_note_id')
                ->join('shipments as s', 'rns.shipment_id', '=', 's.id')
                ->where('s.tracking_number', '=', $tracking_number);
        }
        if ($return_note_number = $request->get('return_note_number')) {
            $datatables->where('return_notes.id', '=', $return_note_number);
        }

        return $datatables->make(true);

    }

    public function return_receive_update(Request $request,$id){
        $service_type = BookingType::all();
        return view('admin.return.receive_update')->with(['return_note_id'=>$id,'service_type'=>$service_type]);
    }

    public function return_receive_update_list(Request $request,$id){
        $deliveries = ReturnNote::join('return_note_shipments as dns','dns.return_note_id','=','return_notes.id')
            ->join('shipments','shipments.id','=','dns.shipment_id')
            ->join('cities AS oc', 'shipments.consignee_city_id', '=', 'oc.id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->select(['return_notes.id as return_note','shipments.tracking_number','shipments.id as shId','oc.name as destination','shipments.consignee_name','shipments.consignee_phone_number_1 as phone','shipments.consignee_address as address','bt.booking_type as service_type', 'dns.status as shipment_status'])
            ->where('return_notes.id',$id);

        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('return_notes.hub_id', session('hubs'));
        }

        return Datatables::of($deliveries)
            ->addColumn("action", function ($deliveries) {
                if ($deliveries->shipment_status == 0){
                    return "<a href='javascript:void(0);' class='returnnoterow'>Remove</a>";
                }
                else{
                    return "";
                }
            })
            ->filterColumn('service_type',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('bt.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->make(true);
    }

    public function return_receive_update_remove(Request $request){
//        return $request;
        $shipment = ReturnNoteShipment::where(['return_note_id'=>$request->return_note_id,'shipment_id'=>$request->shipment_id]);
        if($shipment->exists()){
            $shipment = $shipment->first();
            $return_note = $shipment->return_note_id;
            $return = ReturnNote::where('id',$return_note);
            if($return->exists()){
                $parcel = Shipment::where('id',$request->shipment_id);
//                return $parcel->get();
                $parcel = $parcel->first();
                $shipper_status = '';
                if($parcel->shipper_status_id == 23){
                    $shipper_status = 44;
                }else if($parcel->shipper_status_id == 28){
                    $shipper_status = 45;
                }else if($parcel->shipper_status_id == 34){
                    $shipper_status = 46;
                }
                ReturnNoteShipment::where(['return_note_id'=>$return_note,'shipment_id'=>$request->shipment_id])->delete();
                $return = $return->first();
                $count = $return->shipments_count;
                $count = $count - 1;
                if($count == 0){
                    ReturnNote::where('id',$return_note)->update(['shipments_count'=>$count,'status'=>2]);
                }else{

                    ReturnNote::where('id',$return_note)->update(['shipments_count'=>$count]);
                }
                Shipment::where('id',$request->shipment_id)->update(['shipper_status_id'=>$shipper_status]);
                ShipmentsJourneyController::add($parcel->id, $shipper_status, NULL, NULL, NULL, NULL, Auth::id(),$request->return_note_id);

                $updated_shipments = ReturnNoteShipment::where('return_note_id', $request->return_note_id)->where('status', 0)->count();
                if($updated_shipments == 0){
                    $return_note_data = ReturnNote::where('id', $request->return_note_id)->where('status', '!=', 2)->first();
                    if($return_note_data){
                        $return_note_data->status = 2;
                        $return_note_data->updated_at = Carbon::now();
                        $return_note_data->save();
                    }

                }

                return ['status' => 0, 'success' => 'Return Shipment is successfully removed'];
            }else{
                return ['status' => 1, 'error' => 'Something went wrong'];
            }
        }else{
            return ['status' => 1, 'error' => 'Something went wrong'];
        }
    }

    public function return_receive_status(Request $request,$id){
        $return = ReturnNote::where('id',$id);
        if($return->exists()){
            $return = $return->first();
            $shipment_status = ShipmentStatus::whereIn('id', [24,25, 47, 48, 60])->get();

            return view('admin.return.receive_status')->with(['return_note_id'=>$id,'shipments_count'=>$return->shipments_count, 'return_note_status' => $return->status, 'shipment_statuses' => $shipment_status]);
        }else{
            return redirect()->route('admin.return.receive.index')->with(['error' => 'Return Note not found']);
        }
    }

    public function return_receive_status_list(Request $request){
        $deliveries = ReturnNote::join('return_note_shipments as dns','dns.return_note_id','=','return_notes.id')
            ->join('shipments','shipments.id','=','dns.shipment_id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->leftjoin('user_shipping_infos AS rsi', 'shipments.return_address_id', '=', 'rsi.id')
            ->leftjoin('cities AS rc', 'rsi.city_id', '=', 'rc.id')
            ->join('users','shipments.user_id','=','users.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->join('shipment_status as ss','ss.id','=','shipments.shipper_status_id')
            ->leftjoin('crm_requests as crm', function ($join) {
                $join->on('crm.shipment_id', '=', 'shipments.id')
                    ->whereIn('crm.status_id', [2, 3, 5])
                    ->where('crm.case_nature_id', 1);
            })
            ->leftjoin('shipments_journey as rrb', function ($join) {
                $join->on('rrb.shipment_id', '=', 'shipments.id')
                    ->whereIn('shipments.shipper_status_id', [25, 31, 38])
                    ->where('rrb.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id)'));
            })
            ->select(['return_notes.id as return_note','shipments.tracking_number','shipments.id as shId','oc.name as destination','usi.pickup_address as address','users.name as shipper','bt.booking_type as service_type','shipments.booking_type_id','shipments.shipper_status_id','ss.name as current_status_name', 'usi.poc', 'shipments.charges_mode_id', 'shipments.amount', 'shipments.return_charges','crm.id as complaint', 'rrb.received_or_refused_by', 'rrb.remarks as remarks', 'rsi.pickup_address as return_address_location', 'rc.name as return_city_name'])
            ->where('return_notes.id',$request->id);

        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('return_notes.hub_id', session('hubs'));
        }

        return Datatables::of($deliveries)
            ->setRowAttr([
                'class' => function ($deliveries) {
                    if($deliveries->complaint != null){
                        return 'complaint_row';
                    } else {
                        return '';
                    }
                }
            ])
            ->addColumn('return_note_flag', function ($deliveries){
                $flag = TRUE;
                if(ReturnNoteShipment::where('return_note_id', '>', $deliveries->return_note)->where('shipment_id', $deliveries->shId)->exists()){
                    $flag = FALSE;
                }
                return $flag;
            })
            ->addColumn('shipment_id_padded', function ($deliveries) {
                return str_pad($deliveries->shId, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('shipper', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    return $shipment->shipper .' (' . $shipment->poc . ')';
                }
                else {
                    return $shipment->shipper;
                }
            })
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
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
            ->addColumn('return_address', function ($deliveries) {
                if($deliveries->return_address_location != NULL){
                    return $deliveries->return_address_location;
                }
                else{
                    return $deliveries->address;
                }

            })
            ->addColumn('return_city', function ($deliveries) {
                if($deliveries->return_city_name != NULL){
                    return $deliveries->return_city_name;
                }
                else{
                    return $deliveries->destination;
                }
            })

            ->addColumn('status', function ($deliveries) {
                $delivered_array = array(25,31,38);
                $return_array = array(23, 25);
                if(in_array($deliveries->shipper_status_id,$delivered_array)){
                    return $deliveries->current_status_name;
                }else{
                    if(in_array($deliveries->booking_type_id,[1,4,5])){
                        $where = array(24,47,48, 60);
                    }else if($deliveries->booking_type_id == 2){
                        $where = array(47, 48, 60);
                        if(in_array($deliveries->shipper_status_id,$return_array)){
                            $where[] = 25;
                        }
                        else{
                            $where[] = 29;
                        }

                    }else if($deliveries->booking_type_id == 3){
                        $where = array(35,47,48, 60);
                    }
                    $statuses = ShipmentStatus::whereIn('id',$where)->get();
                    $drops = '';
                    foreach ($statuses as $status){
                        $drops .= '<option value="'.$status->id.'">'.$status->name.'</option>';
                    }
                    $select = '<select class="form-control form-control-sm select2 statusDrop" name="status_drop['.$deliveries->shId.']" ><option></option>'.$drops.'</select>';
                    return $select;
                }

            })
            ->addColumn('reason', function ($deliveries) {
                $delivered_array = array(25,31,38);
                if(in_array($deliveries->shipper_status_id,$delivered_array)) {
                    return '';
                }else{
                    $reason = '<select class="form-control form-control-sm select2 reasonDrop" name="reason_drop['.$deliveries->shId.']" ><option></option></select>';
                    return $reason;
                }

            })
            ->addColumn('remarks', function ($deliveries) {
                $delivered_array = array(25,31,38);
                if(in_array($deliveries->shipper_status_id,$delivered_array)) {
                    return $deliveries->remarks;
                }else{
                    $reason = '<input class="form-control form-control-sm" name="remarks['.$deliveries->shId.']" placeholder="Enter Remarks">';
                    return $reason;
                }

            })
            ->addColumn('received_or_refused_by', function ($deliveries) {
                $delivered_array = array(25,31,38);
                if(!in_array($deliveries->shipper_status_id,$delivered_array)) {
                    $received_or_refused_by = '<input class="form-control form-control-sm" name="received_or_refused_by['.$deliveries->shId.']" placeholder="Enter Name">';
                    return $received_or_refused_by;
                }else{
                    return $deliveries->received_or_refused_by;
                }

            })
            ->addColumn('charges', function ($shipment) {
                if ($shipment->booking_type_id == 4) {
                    if ($shipment->charges_mode_id == 1) {
                        return number_format($shipment->return_charges);
                    }
                    else {
                        return number_format($shipment->amount);
                    }
                }
                else {
                    return '';
                }
            })
            ->addColumn('open_box', function ($deliveries){
                $open_box_checkbox = '<input type="checkbox" class="open_box" name="open_box[' . $deliveries->shId . ']">';
                return $open_box_checkbox;
            })
            ->addColumn('action',function($deliveries){
                $delivered_array = array(25,31,38);
                if(in_array($deliveries->shipper_status_id,$delivered_array)) {
                    return '';
                }else{
                    return " <span class='dropdown'>
                                            <button type='button' class='btn btn-success dropdown-toggle' data-toggle='dropdown'
                                                    aria-haspopup='true' aria-expanded='false'><i class='ft-settings'></i></button>
                                            <div class='dropdown-menu open-left arrow'>
                                              <a href='javascript:void(0);' class='dropdown-item clear'><i class='ft-rotate-cw primary'></i> Clear</a>                                         
                                            </div></span>";
                }

            })
            ->make(true);
    }

    public function receive_return_reason(Request $request){

        $status_id = $request->status;
        $statuses = ShipmentStatus::find($status_id)->reasons()->select('id','name')->orderBy('name')->get();

        if(!$statuses->isEmpty()){
            return response()->json(['status'=>0,'reasons'=>$statuses]);
        }else{
            return ['status'=>1,'error'=>'No reasons are defined for this status!'];
        }

    }

    public function receive_return_status_submit(Request $request){
        $open_box_ids = array();
        $new_return_note_shipments = NULL;
        $shipments_updated_flag = FALSE;
        $shipments = explode(',',$request->shipment_ids);
        $open_box_ids = explode(',',$request->open_box_ids);
        $return_note_id = $request->return_note_id;
        $array_returned = array(25,31,38);
        $array_returned_status = array(24,29,35,47,48, 60);
        $actual_date = $request->actual_date_formatted;
        if($return_note_id != '') {
            $return_note_details = ReturnNote::find($return_note_id);

            foreach ($shipments as $shipment) {
                $reasonId = "reason_drop.$shipment";
                $parcel = Shipment::where('id', $shipment)->first();
                if(!ReturnNoteShipment::join('return_notes', 'return_notes.id', '=', 'return_note_shipments.return_note_id')->where('return_note_shipments.return_note_id','>', $return_note_id)->where('shipment_id', $shipment)->exists()){
                    if(count($open_box_ids) > 0){
                        if(in_array($shipment, $open_box_ids)){
                            $parcel->open_box = 1;
                            $parcel->save();
                            ShipmentOpenBoxJourneyController::add($shipment, 7, Auth::id());
                        }
                    }
                    if (!in_array($parcel->shipper_status_id, $array_returned)) {
                        if (in_array($request->status_drop[$shipment],$array_returned_status)) {
                            if($request->status_drop[$shipment] != $parcel->shipper_status_id){
                                ShipmentsJourneyController::add($shipment, $request->status_drop[$shipment], NULL, ($request->has($reasonId) ? $request->reason_drop[$shipment] : null), $request->remarks[$shipment], NULL, Auth::id(), $return_note_id);

                                Shipment::where('id', $shipment)->update(['shipper_status_id' => $request->status_drop[$shipment]]);
                                ReturnNoteShipment::where(['return_note_id' => $return_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);
                                $shipments_updated_flag = TRUE;
                            }
                        }

                    }
                }else{
                    $new_return_note_shipments .= $parcel->tracking_number . ' ';
                }
            }
            $shipment_status = ReturnNoteShipment::where(['return_note_id'=>$return_note_id,'status'=>0])->count();
            if($shipment_status == 0){
                if($return_note_details->completion_status == 0){
                    $return_note_details->status = 1;
                    $return_note_details->updated_by = Auth::id();
                }else{
                    $return_note_details->status = 3;
                    $return_note_details->updated_by = Auth::id();
                }
                $return_note_details->actual_date = $actual_date;
                $return_note_details->save();
            }

            NotificationsController::send(15, $return_note_id);
            NotificationsController::send(16, $return_note_id);

            if($new_return_note_shipments != null){
                if($shipments_updated_flag){
                    return redirect()->back()->with(['success'=>'Return Note Status Has Been Updated','error' => 'Following shipments are already in new return note '.$new_return_note_shipments]);

                }else{
                    return redirect()->back()->with(['error' => 'Following shipments are already in new return note '.$new_return_note_shipments]);

                }
            }else{
                return redirect()->back()->with(['success'=>'Return Note Status Has Been Updated']);
            }
        }
    }

    public function return_status_delivered(Request $request){
        if(!empty($request->shipment_ids)){
            $open_box_ids = array();
            $actual_date = $request->actual_date;
            if($request->has('open_box_ids')){
                $open_box_ids = $request->open_box_ids;
            }
            $return_note_details = ReturnNote::find($request->return_note_id);
            foreach ($request->shipment_ids as $shipment){
                $parcel = Shipment::where('id', $shipment)->first();
                $shipment_remark = "remarks.$shipment";
                $received_or_refused_by = "received_or_refused_by.$shipment";
                if(!ReturnNoteShipment::join('return_notes', 'return_notes.id', '=', 'return_note_shipments.return_note_id')->where('return_note_shipments.return_note_id','>', $request->return_note_id)->where('shipment_id', $shipment)->exists()) {
                    if(count($open_box_ids) > 0){
                        if(in_array($shipment, $open_box_ids)){
                            $parcel->open_box = 1;
                            $parcel->save();
                            ShipmentOpenBoxJourneyController::add($shipment, 7,Auth::id());
                        }
                    }
                    if ($parcel->booking_type_id == 1 || $parcel->booking_type_id == 4 || $parcel->booking_type_id == 5) {
                        ShipmentsJourneyController::add($shipment, 25, 25, NULL, ($request->has($shipment_remark) ? $request->remarks[$shipment] : null), NULL, Auth::id(), $request->return_note_id, NULL, 1, ($request->has($received_or_refused_by) ? $request->received_or_refused_by[$shipment] : null));

                        Shipment::where('id', $shipment)->update(['shipper_status_id' => 25, 'consignee_status_id' => 25]);
                        ReturnNoteShipment::where(['return_note_id' => $request->return_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);

//                        $packaging_material_shipment = PackagingMaterialRequest::where('tracking_number', $parcel->tracking_number)->first();
//                        if ($packaging_material_shipment != null) {
//                            $request_id = $packaging_material_shipment->id;
//
//                            $packaging_material_request = PackagingMaterialRequest::where('id', $request_id)->with('city')->first();
//
//                            if ($packaging_material_shipment->status_id == 3) {
//                                $packaging_material_request_details = PackagingMaterialRequestDetail::where('packaging_material_request_id', $request_id)->get();
//
//                                $hub_id = $packaging_material_request->city->hub_id;
//
//                                $fulfilment_hub = WarehouseFulfilmentHubs::where('hub_id', $hub_id)->first();
//
//                                $warehouse_id = $fulfilment_hub->warehouse_id;
//
//                                foreach ($packaging_material_request_details as $detail_add) {
//                                    $type_id = $detail_add->type_id;
//                                    $type_size_id = $detail_add->type_size_id;
//                                    $stock = WarehouseStock::where(['warehouse_id' => $warehouse_id, 'type_id' => $type_id, 'type_size_id' => $type_size_id]);
//
//                                    $stock = $stock->first();
//                                    $stock->stock = $stock['stock'] + $detail_add->quantity;
//                                    $stock->save();
//                                }
//                            }
//                            $packaging_material_request->status_id = 5;
//                            $packaging_material_request->save();
//
//
//                            $packaging_request_history = new PackagingMaterialRequestHistory();
//                            $packaging_request_history->packaging_material_request_id = $request_id;
//                            $packaging_request_history->status = 5;
//                            $packaging_request_history->updated_by = Auth::id();
//                            $packaging_request_history->save();
//                        }

                    } else if ($parcel->booking_type_id == 2) {

                        if(in_array($parcel->shipper_status_id, [23, 24])){
                            $shipper_status_id = 25;
                            $consignee_status_id = 25;
                        }
                        else{
                            $shipper_status_id = 31;
                            $consignee_status_id = 31;
                        }
                        ShipmentsJourneyController::add($shipment, $shipper_status_id, $consignee_status_id, NULL, ($request->has($shipment_remark) ? $request->remarks[$shipment] : null), NULL, Auth::id(), $request->return_note_id, NULL, 1, ($request->has($received_or_refused_by) ? $request->received_or_refused_by[$shipment] : null));

                        Shipment::where('id', $shipment)->update(['shipper_status_id' => $shipper_status_id, 'consignee_status_id' => $consignee_status_id]);
                        ReturnNoteShipment::where(['return_note_id' => $request->return_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);

                    } else if ($parcel->booking_type_id == 3) {
                        ShipmentsJourneyController::add($shipment, 38, 38, NULL, NULL, NULL, Auth::id(), $request->return_note_id, NULL, 1, ($request->has($received_or_refused_by) ? $request->received_or_refused_by[$shipment] : null));

                        Shipment::where('id', $shipment)->update(['shipper_status_id' => 38, 'consignee_status_id' => 38]);
                        ReturnNoteShipment::where(['return_note_id' => $request->return_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);

                    } else {
                        ShipmentsJourneyController::add($shipment, 25, 25, NULL, ($request->has($shipment_remark) ? $request->remarks[$shipment] : null), NULL, Auth::id(), $request->return_note_id, NULL, 1, ($request->has($received_or_refused_by) ? $request->received_or_refused_by[$shipment] : null));

                        Shipment::where('id', $shipment)->update(['shipper_status_id' => 25, 'consignee_status_id' => 25]);
                        ReturnNoteShipment::where(['return_note_id' => $request->return_note_id, 'shipment_id' => $shipment])->update(['status' => 1]);

                    }
                    if($return_note_details->completion_status == 0){
                        $return_note_details->completion_status = 1;
                        $return_note_details->save();
                    }
                }
            }
            $shipment_status = ReturnNoteShipment::where(['return_note_id'=>$request->return_note_id,'status'=>0])->count();
            if($shipment_status == 0){
                $return_note_details->status = 3;
                $return_note_details->updated_by = Auth::id();
            }
            $return_note_details->actual_date = $actual_date;
            $return_note_details->save();

            NotificationsController::send(15, $request->return_note_id);
            NotificationsController::send(16, $request->return_note_id);

            return ['status'=>0,'success'=>'Return note shipments status are updated to : Delivered to Shipper'];
        }else{
            return ['status'=>1,'error'=>'No shipments selected'];

        }
    }

    public function receive_return_status_submit_all(Request $request){
        if(!empty($request->shipment_ids)){
            $shipment_ids = $request->shipment_ids;
            $shipment_status = $request->shipment_status;
            $shipment_reason = $request->shipment_reason;
            $actual_date = $request->actual_date;
            $reason_mandatory_shipments = array();
            $shipment_status_mandatory = array(24,47,48);
            $mandatory_shippers = ReturnReasonMandatoryShipper::pluck('shipper_id')->toArray();
            $open_box_ids = array();
            if($request->has('open_box_ids')){
                $open_box_ids = $request->open_box_ids;
            }
            $return_note_details = ReturnNote::find($request->return_note_id);
            if($shipment_status == 25){
                foreach ($shipment_ids as $shipment_id) {
                    $parcel = Shipment::where('id', $shipment_id)->first();
                    $shipment_remark = "remarks.$shipment_id";
                    $received_or_refused_by = "received_or_refused_by.$shipment_id";
                    if(count($open_box_ids) > 0){
                        if(in_array($shipment_id, $open_box_ids)){
                            $parcel->open_box = 1;
                            $parcel->save();
                            ShipmentOpenBoxJourneyController::add($shipment_id, 7,Auth::id());
                        }
                    }
                    if(!ReturnNoteShipment::join('return_notes', 'return_notes.id', '=', 'return_note_shipments.return_note_id')->where('return_note_shipments.return_note_id','>', $request->return_note_id)->where('shipment_id', $shipment_id)->exists()) {

                        if ($parcel->booking_type_id == 2) {
                            $shipper_status_id = 31;
                            $consignee_status_id = 31;
                            if(in_array($parcel->shipper_status_id, [23, 24])){
                                $shipper_status_id = 25;
                                $consignee_status_id = 25;
                            }
                            ShipmentsJourneyController::add($shipment_id, $shipper_status_id, $consignee_status_id, $shipment_reason, ($request->has($shipment_remark) ? $request->remarks[$shipment_id] : null), NULL, Auth::id(), $request->return_note_id, NULL, 1, ($request->has($received_or_refused_by) ? $request->received_or_refused_by[$shipment_id] : null));

                            Shipment::where('id', $shipment_id)->update(['shipper_status_id' => $shipper_status_id, 'consignee_status_id' => $consignee_status_id]);

                        } else if ($parcel->booking_type_id == 3) {
                            ShipmentsJourneyController::add($shipment_id, 38, 38, $shipment_reason, NULL, NULL, Auth::id(), $request->return_note_id, NULL, 1, ($request->has($received_or_refused_by) ? $request->received_or_refused_by[$shipment_id] : null));

                            Shipment::where('id', $shipment_id)->update(['shipper_status_id' => 38, 'consignee_status_id' => 38]);

                        } else {
                            ShipmentsJourneyController::add($shipment_id, 25, 25, $shipment_reason, ($request->has($shipment_remark) ? $request->remarks[$shipment_id] : null), NULL, Auth::id(), $request->return_note_id, NULL, 1, ($request->has($received_or_refused_by) ? $request->received_or_refused_by[$shipment_id] : null));

                            Shipment::where('id', $shipment_id)->update(['shipper_status_id' => 25, 'consignee_status_id' => 25]);
//                            if($parcel->packaging_material_request){
//                                $packaging_material_shipment = PackagingMaterialRequest::where('tracking_number', $parcel->tracking_number)->first();
//                                if ($packaging_material_shipment != null) {
//                                    $request_id = $packaging_material_shipment->id;
//
//                                    $packaging_material_request = PackagingMaterialRequest::where('id', $request_id)->with('city')->first();
//
//                                    if ($packaging_material_shipment->status_id == 3) {
//                                        $packaging_material_request_details = PackagingMaterialRequestDetail::where('packaging_material_request_id', $request_id)->get();
//
//                                        $hub_id = $packaging_material_request->city->hub_id;
//
//                                        $fulfilment_hub = WarehouseFulfilmentHubs::where('hub_id', $hub_id)->first();
//
//                                        $warehouse_id = $fulfilment_hub->warehouse_id;
//
//                                        foreach ($packaging_material_request_details as $detail_add) {
//                                            $type_id = $detail_add->type_id;
//                                            $type_size_id = $detail_add->type_size_id;
//                                            $stock = WarehouseStock::where(['warehouse_id' => $warehouse_id, 'type_id' => $type_id, 'type_size_id' => $type_size_id]);
//
//                                            $stock = $stock->first();
//                                            $stock->stock = $stock['stock'] + $detail_add->quantity;
//                                            $stock->save();
//                                        }
//                                    }
//                                    $packaging_material_request->status_id = 5;
//                                    $packaging_material_request->save();
//
//
//                                    $packaging_request_history = new PackagingMaterialRequestHistory();
//                                    $packaging_request_history->packaging_material_request_id = $request_id;
//                                    $packaging_request_history->status = 5;
//                                    $packaging_request_history->updated_by = Auth::id();
//                                    $packaging_request_history->save();
//                                }
//                            }

                        }
                        // ReturnNoteShipment::where(['return_note_id' => $request->return_note_id, 'shipment_id' => $shipment_id])->update(['status' => 1]);
                    }
                    ReturnNoteShipment::where(['return_note_id'=>$request->return_note_id,'shipment_id'=>$shipment_id])->update(['status'=>1]);
                    if($return_note_details->completion_status == 0){
                        $return_note_details->completion_status = 1;
                        $return_note_details->save();
                    }
                }
                $shipment_status_count = ReturnNoteShipment::where(['return_note_id'=>$request->return_note_id,'status' => 0])->count();
                if($shipment_status_count == 0){
                    $return_note_details->updated_by = Auth::id();
                    $return_note_details->status = 3;
                }
                $return_note_details->actual_date = $actual_date;
                $return_note_details->save();

                NotificationsController::send(15, $request->return_note_id);
                NotificationsController::send(16, $request->return_note_id);
                return response()->json(['status'=> 0, 'success' => 'Return note shipments status are updated to : Delivered to Shipper']);
            }
            else{
                $remarks = $request->remarks;
                foreach ($shipment_ids as $shipment_id) {
                    $shipment = Shipment::find($shipment_id);
                    if(in_array($shipment_status,$shipment_status_mandatory) && in_array($shipment->user_id,$mandatory_shippers) && $shipment_reason == null){
                        array_push($reason_mandatory_shipments, $shipment->tracking_number);
                    }
                    else{
                        $shipment->shipper_status_id = $shipment_status;
                        $shipment->save();
                        ShipmentsJourneyController::add($shipment_id, $shipment_status, NULL, $shipment_reason, $remarks, NULL, Auth::id(), $request->return_note_id);


                        ReturnNoteShipment::where(['return_note_id' => $request->return_note_id, 'shipment_id' => $shipment_id])->update(['status' => 1]);
                        if(count($open_box_ids) > 0){
                            if(in_array($shipment_id, $open_box_ids)){
                                $shipment->open_box = 1;
                                $shipment->save();
                                ShipmentOpenBoxJourneyController::add($shipment_id, 7,Auth::id());
                            }
                        }
                    }
                }
                $shipment_status = ReturnNoteShipment::where(['return_note_id'=>$request->return_note_id,'status'=>0])->count();
                if($shipment_status == 0){
                    if($return_note_details->completion_status == 0){
                        $return_note_details->status = 1;
                        $return_note_details->updated_by = Auth::id();

                    }else{
                        $return_note_details->status = 3;
                        $return_note_details->updated_by = Auth::id();
                    }
                    $return_note_details->actual_date = $actual_date;
                    $return_note_details->save();

                }

                NotificationsController::send(15, $request->return_note_id);
                NotificationsController::send(16, $request->return_note_id);
                if(count($reason_mandatory_shipments) > 0){
                    return response()->json(['status'=> 2, 'success' => 'Return Note Status Has Been Updated', 'reason_mandatory_shipments' => $reason_mandatory_shipments]);
                }else{
                    return response()->json(['status'=> 0, 'success' => 'Return Note Status Has Been Updated']);
                }
            }
        }
    }

    public function rrd_print(Request $request){

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

                      /*table.table-bordered {
                        page-break-inside: avoid;
                      }*/

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
                        if($filtered_shipments_user->return_address_id != NULL){
                            $shipment_details_row_start_summary = '
                                  <tr>
                                    <td>' . $total_users . '</td>
                                    <td>' . $filtered_shipments_user->user->name . ' | ' . $filtered_shipments_user->user->phone . (($filtered_shipments_user->user->phone2) ? (' / ' . $filtered_shipments_user->user->phone2) : '') . '</td>
                                   
                                   
                                   
                                    <td>' . $filtered_shipments_user->return_address->poc . '</td>
                                    <td>' . $filtered_shipments_user->return_address->phone . '</td>
                                    <td>' . $filtered_shipments_user->return_address->pickup_address . '</td>
                                    <td>' . $user_total_shipments[$filtered_shipments_user->user_id] . '</td>
                                    <td></td>
                        ';
                        }
                        else{
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
                        }


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
                        if($filtered_shipments_user->return_address_id != NULL){
                            $shipment_details_row_start_summary = '
                              <tr>
                                <td>' . $replacement_total_users . '</td>
                                <td>' . $filtered_shipments_user->user->name . ' | ' . $filtered_shipments_user->user->phone . (($filtered_shipments_user->user->phone2) ? (' / ' . $filtered_shipments_user->user->phone2) : '') . '</td>
                                <td>' . $filtered_shipments_user->return_address->poc . '</td>
                                <td>' . $filtered_shipments_user->return_address->phone . '</td>
                                <td>' . $filtered_shipments_user->return_address->pickup_address . '</td>
                                <td>' . $user_total_shipments[$filtered_shipments_user->user_id] . '</td>
                                <td></td>
                    ';
                        }
                        else{
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
                        }


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
                          <table class="table table-sm table-bordered border" style="display:table-row-group;page-break-inside:avoid;page-break-after:auto;">
                            <tbody>
                                <tr>
                                    <td class="color primary" colspan="10"><strong style="font-size: large">' . $filtered_shipments_user->user->name . '</strong></td>
                                </tr>
                              <tr>
                                <td class="color primary"><strong>S. No.</strong></td>
                                <td class="color primary"><strong>Tracking No.</strong></td>
                                
                                
                                <td class="color primary"><strong>Client Name & Phone No(s).</strong></td>
                                <td class="color primary"><strong>Order ID.</strong></td>
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
                        if($shipment->return_address_id != NULL){
                            $shipment_details_row_start = '
                              <tr>
                                <td>' . $total_shipments . '</td>
                                <td class="'. $class .'">' . $shipment->tracking_number . '</td>
                                <td>' . $shipment->user->name . ' | ' . $shipment->user->phone . (($shipment->user->phone2) ? (' / ' . $shipment->user->phone2) : '') . '</td>
                                <td>' . $shipment->order_id . '</td>
                                
                                <td>' . $shipment->return_address->poc . '</td>
                                
                                <td>' . $shipment->return_address->phone . '</td>
                                <td>' . $shipment->return_address->pickup_address . '</td>
                                <td>' . $shipment->items->where('bought', 0)->sum('quantity') . '</td>
                    ';
                        }
                        else{
                            $shipment_details_row_start = '
                              <tr>
                                <td>' . $total_shipments . '</td>
                                <td class="'. $class .'">' . $shipment->tracking_number . '</td>
                                <td>' . $shipment->user->name . ' | ' . $shipment->user->phone . (($shipment->user->phone2) ? (' / ' . $shipment->user->phone2) : '') . '</td>
                                <td>' . $shipment->order_id . '</td>
                                <td>' . $shipment->pickup_address->poc . '</td>
                                <td>' . $shipment->pickup_address->phone . '</td>
                                <td>' . $shipment->pickup_address->pickup_address . '</td>
                                <td>' . $shipment->items->where('bought', 0)->sum('quantity') . '</td>
                    ';
                        }


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
            $rider_id = $rider->trax_id;
            $category = $rider->rider_category->name;
            $route_name = $return_note_details->route->code .' ('.$return_note_details->route->start.' to '.$return_note_details->route->end.')';
            $main_details = '
                      <table class="table table-sm table-bordered border">
                        <tbody>
                          <tr>
                            <td class="text-center align-middle"><img src="' . asset('img/trax_logo_new.png') . '" width="100" class="d-block mx-auto"></td>
                            <td class="text-center align-middle color primary"><strong>Return Note</strong></td>
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
                            <td class="color secondary"><strong>Rider Trax ID</strong></td>
                            <td>' . $rider_id . '</td>
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

    public function return_confirmed_revert(Request $request) {

        $shipment = Shipment::find($request->id);
        $dispute_check = CheckDisputeShipmentsController::check($shipment->id);
        if(!$dispute_check){
            return ['status' => 1, 'error' => 'Shipment is in Dispute, please resolve dispute first!'];
        }
        $flag = true;
        $consolidation = ConsolidationShipments::where('shipment_id', $shipment->id)->first();
        if($consolidation){
            $consolidation_shipments = ConsolidationShipments::where('consolidation_id', $consolidation->consolidation_id)->get();
            foreach ($consolidation_shipments as $consolidation_shipment){
                $is_shipment = Shipment::find($consolidation_shipment->shipment_id);
                if($is_shipment->shipper_status_id == 23){
                    $flag = false;
                }
            }
        }
        if($flag == true) {
            if ($shipment->shipper_status_id == 20) {
                if($consolidation){
                    $consolidation_shipments = ConsolidationShipments::where('consolidation_id', $consolidation->consolidation_id)->get();
                    foreach ($consolidation_shipments as $consolidation_shipment){
                        $is_shipment = Shipment::find($consolidation_shipment->shipment_id);

                        $is_shipment->shipper_status_id = 13;
                        $is_shipment->consignee_status_id = 13;

                        $is_shipment->save();
                        $is_journey = ShipmentsJourney::where('shipment_id', $is_shipment->id)->where('shipper_status_id', 20)->latest()->first();
                        if ($is_journey) {
                            $return_reattempt = new ReturnReattemptRatio();
                            $return_reattempt->shipment_id = $is_shipment->id;
                            $return_reattempt->return_confirm_date = $is_journey->created_at;
                            $return_reattempt->save();
                        }

                        ShipmentsJourneyController::add($is_shipment->id, 13, 13, NULL, $request->remarks, NULL, Auth::id());
                        if($is_shipment->shipment_type == 1) {
                            AdminFinanceController::return_confirmed_revert($is_shipment->id, 1);
                        }
                    }
                }
                else{
                    $shipment->shipper_status_id = 13;
                    $shipment->consignee_status_id = 13;

                    $shipment->save();

                    $journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 20)->latest()->first();
                    if ($journey) {
                        $return_reattempt = new ReturnReattemptRatio();
                        $return_reattempt->shipment_id = $shipment->id;
                        $return_reattempt->return_confirm_date = $journey->created_at;
                        $return_reattempt->save();
                    }


                    ShipmentsJourneyController::add($request->id, 13, 13, NULL, $request->remarks, NULL, Auth::id());
                    if($shipment->shipment_type == 1){
                        AdminFinanceController::return_confirmed_revert($request->id, 1);
                    }
                }

                return ['status' => 0, 'success' => 'Shipment has been Reverted'];
            } else {
                return ['status' => 1, 'error' => 'Shipment has already been Reverted'];
            }
        }
        else {
            return ['status' => 1, 'error' => 'Consolidated Shipment found in Return Note'];
        }
    }

    public function return_revert_status(Request $request)
    {
        if($request->action=='revert'){
            foreach($request->shipment_ids as $shipments)
            {
                $shipment = Shipment::find($shipments);
                $flag = true;
                $consolidation = ConsolidationShipments::where('shipment_id', $shipment->id)->first();
                if($consolidation){
                    $consolidation_shipments = ConsolidationShipments::where('consolidation_id', $consolidation->consolidation_id)->get();
                    foreach ($consolidation_shipments as $consolidation_shipment){
                        $is_shipment = Shipment::find($consolidation_shipment->shipment_id);
                        if($is_shipment->shipper_status_id == 23){
                            $flag = false;
                        }
                    }
                }
                if($flag == true) {
                    if ($shipment->shipper_status_id == 20) {
                        if($consolidation){
                            $consolidation_shipments = ConsolidationShipments::where('consolidation_id', $consolidation->consolidation_id)->get();
                            foreach ($consolidation_shipments as $consolidation_shipment){
                                $is_shipment = Shipment::find($consolidation_shipment->shipment_id);

                                $is_shipment->shipper_status_id = 13;
                                $is_shipment->consignee_status_id = 13;

                                $is_shipment->save();
                                $is_journey = ShipmentsJourney::where('shipment_id', $is_shipment->id)->where('shipper_status_id', 20)->latest()->first();
                                if ($is_journey) {
                                    $return_reattempt = new ReturnReattemptRatio();
                                    $return_reattempt->shipment_id = $is_shipment->id;
                                    $return_reattempt->return_confirm_date = $is_journey->created_at;
                                    $return_reattempt->save();
                                }

                                ShipmentsJourneyController::add($is_shipment->id, 13, 13, NULL, NULL, NULL, Auth::id());
                                if($is_shipment->shipment_type == 1) {
                                    AdminFinanceController::return_confirmed_revert($is_shipment->id, 1);
                                }
                            }
                        }
                        else{
                            $shipment->shipper_status_id = 13;
                            $shipment->consignee_status_id = 13;

                            $shipment->save();

                            $journey = ShipmentsJourney::where('shipment_id', $shipment->id)->where('shipper_status_id', 20)->latest()->first();
                            if ($journey) {
                                $return_reattempt = new ReturnReattemptRatio();
                                $return_reattempt->shipment_id = $shipment->id;
                                $return_reattempt->return_confirm_date = $journey->created_at;
                                $return_reattempt->save();
                            }


                            ShipmentsJourneyController::add($shipments, 13, 13, NULL, NULL, NULL, Auth::id());
                            if($shipment->shipment_type == 1){
                                AdminFinanceController::return_confirmed_revert($shipments, 1);
                            }
                        }
                    }
                }
            }
            return ['status' => 0, 'success' => 'Shipment has been Reverted'];
        }
    }
    public function receive_return_shipments(Request $request){
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

    public function history_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),28);
        return view('admin.return.history');
    }

    public function history_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 88);
        }

        $deliveries = ReturnNote::
        join('cities AS oc', 'return_notes.hub_id', '=', 'oc.id')
            ->join('riders', 'return_notes.rider_id', '=', 'riders.id')
            ->join('admins', 'admins.id', '=', 'return_notes.admin_id')
            ->leftjoin('admins as sb', 'sb.id', '=', 'return_notes.updated_by')
            ->select(['return_notes.id as return_note', 'return_notes.id as return_note_id', 'oc.name as hub', 'riders.name as rider', 'admins.name as assigned_by', 'return_notes.created_at', 'return_notes.shipments_count', 'return_notes.shipments_count as shipments_count_link', 'return_notes.status', 'sb.name as submitted_by', 'return_notes.updated_at', 'return_notes.updated_at as submitted_at', 'return_notes.image', DB::raw('(SELECT COUNT(id) FROM shipments_journey where shipper_status_id in (25,31,38) and reference_1_id = return_notes.id and verification = 1 ) as delivered_to_shipper_count')]);

        if (session('role_id') != 1) {
            $deliveries = $deliveries->whereIn('oc.hub_id', session('hubs'));
        }

        $datatables = Datatables::of($deliveries)
            ->editColumn('return_note', function ($deliveries) {
                return "<a href='javascript:void(0);' class='printreturnnote'><u>" . str_pad($deliveries->return_note, 6, '0', STR_PAD_LEFT) . "</u></a>";
            })
            ->addColumn('return_note_id_padded', function ($deliveries) {
                return str_pad($deliveries->return_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('image', function ($deliveries) {
                if ($deliveries->delivered_to_shipper_count != 0) {
                    return "<a href='#' class='btn btn-block btn-outline-info mr-1 image-popup'><i class='la la-image'></i></a>";
                } else {
                    return '-';
                }
            })
            ->editColumn('shipments_count_link', function ($deliveries) {
                if ($deliveries->shipments_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->shipments_count . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('delivered_to_shipper_count', function ($deliveries) {
                if ($deliveries->delivered_to_shipper_count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $deliveries->delivered_to_shipper_count . '</button>';
                } else {

                    return '-';
                }
            })
            ->filterColumn('return_notes.id', function ($query, $keyword) {
                return $query->where('return_notes.id', '=', $keyword);
            })
            ->addColumn('main_status', function ($deliveries) {
                if ($deliveries->status == 0) {
                    return 'Pending for Update';
                } else if ($deliveries->status == 1) {
                    return 'Verified';
                } else if ($deliveries->status == 2) {
                    return 'Canceled';
                } else if ($deliveries->status == 3) {
                    return 'Updated';
                }
            })
            ->filterColumn('main_status', function ($query, $keyword) {
                if ($keyword == 0) {
                    $query->where('return_notes.status', 0);
                } else if ($keyword == 1) {
                    $query->where('return_notes.status', 1);
                } else if ($keyword == 2) {
                    $query->where('return_notes.status', 2);
                } else if ($keyword == 3) {
                    $query->where('return_notes.status', 3);
                }
            });
        if ($request->get('search_date_from') && $request->get('search_date_to')) {
            $from = $request->get('search_date_from');
            $to = $request->get('search_date_to');
            $datatables->whereBetween('return_notes.created_at', [$from, $to]);
        }

        return $datatables->make(true);
    }

    public function history_shipments(Request $request){
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
    public function history_delivered_shipments(Request $request){
        $return_note_id = $request->input('delivered_to_shipper_count');
        $return_note_shipments = ShipmentsJourney::where('reference_1_id', $return_note_id)->where('shipper_status_id', 25)->where('verification', 1)->pluck('shipment_id')->toArray();
        $shipments = array();
        if(count($return_note_shipments) > 0){
            foreach ($return_note_shipments as $shipment_id){
                $shipment = Shipment::find($shipment_id);
                $shipments[] = $shipment->tracking_number;
            }
            return ['status' => 0, 'success' => 'Return Note Shipments', 'shipments' => $shipments];
        }else{
            return ['status' => 0, 'success' => 'No Return Note Shipments', 'shipments' => FALSE];
        }
}

    public function receive_return_note_image_upload(Request $request){
        
        $flag = FALSE;
        $return_note_id = $request->image_return_note_id;
        $image_ids = explode(',', $request->selected_ids);
        if(count($image_ids) == 0){
            return redirect()->back()->with('error', 'No images selected!');
        }
        $return_note = ReturnNote::find($return_note_id);
        if($return_note){
            if($return_note->status == 3 || $return_note->status == 1){
                foreach ($image_ids as $index => $id) {
                    $file_name = 'return_note_image_'.$id;
                    $image = $request->file($file_name);
                    $imageName = $image->getClientOriginalName();

                    //$imageName = explode('.', $imageName);
                    $extension = $image->getClientOriginalExtension();
                    $random = rand(1000, 100000);
                    $now = Carbon::now();
                    $time = $now->year . '_' . $now->month;
                    $generated_image_name = $time . $random . Auth::id() . '.' . $extension;
                    $image->move(public_path('uploads/return_notes'), $generated_image_name);
                    $return_note_image = new ReturnNoteImage();
                    $return_note_image->return_note_id = $return_note_id;
                    $return_note_image->image = $generated_image_name;
                    $return_note_image->user_id = $request->shipper_name[$index];
                    $return_note_image->save();
                    $flag = TRUE;
                }
                if($return_note->image !== NULL){
                    $return_note_image = new ReturnNoteImage();
                    $return_note_image->return_note_id = $return_note_id;
                    $return_note_image->image = $return_note->image;
                    $return_note_image->image = $request->shipper_name[$index];
                    $return_note_image->save();
                    $return_note->image = NULL;
                    $return_note->save();
                }
                if($flag){
                    $return_note->updated_by = Auth::id();
                    $return_note->status = 1;
                    $return_note->save();
                }
                return redirect()->back()->with(['status' => 1, 'success' => 'Return Note updated successfully']);

            }
            else{
                return redirect()->back()->with(['status' => 0, 'error' => 'Return Note not updated yet!']);
            }

        }
        else{
            return redirect()->back()->with(['status' => 0, 'error' => 'Return Note Not found!']);
        }
    }

    static public function archive_directory(){
        $files = File::glob(public_path() . '/uploads/return_notes/*.*');
        $now = Carbon::now();
        foreach ($files as $file) {
            if (is_file($file)) {
                $created = date("F d Y H:i:s.",filemtime($file));
                $file_name = pathinfo($file);
                if($now->diffInDays($created) > 1){
                    Storage::disk('s3')->put( 'return_note_images/'.$file_name['basename'], file_get_contents($file));
                    $exists = Storage::disk('s3')->exists('return_note_images/'.$file_name['basename']);
                    if($exists){
                        File::delete($file);
                    }
                }
            }
        }

        $files = File::glob(asset('storage/uploads/return_notes/*.*'));
        $now = Carbon::now();
        foreach ($files as $file) {
            if (is_file($file)) {
                $created = date("F d Y H:i:s.",filemtime($file));
                $file_name = pathinfo($file);
                if($now->diffInDays($created) > 1){
                    Storage::disk('s3')->put( 'return_note_images/'.$file_name['basename'], file_get_contents($file));
                    $exists = Storage::disk('s3')->exists('return_note_images/'.$file_name['basename']);
                    if($exists){
                        File::delete($file);
                    }
                }
            }
        }
    }

    public function cx_sales_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),310);
        $shipment_status = ShipmentStatus::select('id','name')->get();
        $shipping_mode = ShippingMode::all();
        $service_type = BookingType::all();
        return view('admin.return.cx_sales')->with(['shipment_status'=>$shipment_status,'shipping_mode'=>$shipping_mode,'service_type'=>$service_type]);
    }

    public function cx_sales_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),311);
        }
        $shipments = Shipment::join('users as u', 'shipments.user_id', '=', 'u.id')
            ->join('user_shipping_infos AS usi', 'shipments.pickup_address_id', '=', 'usi.id')
            ->join('cities AS oc', 'usi.city_id', '=', 'oc.id')
            ->join('cities AS dc', 'shipments.consignee_city_id', '=', 'dc.id')
            ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
            ->join('shipping_modes as sm','sm.id','=','shipments.shipping_mode_id')
            ->join('booking_types as bt','bt.id','=','shipments.booking_type_id')
            ->leftJoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'shipments.id')
                    ->where('shipments_journey.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id order by shipments_journey.created_at desc limit 1)'));
            })
            ->leftJoin('shipments_journey as sj', function ($join) {
                $join->on('sj.shipment_id', '=', 'shipments.id')
                    ->where('sj.id','=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = shipments.id and shipments_journey.shipper_status_id = 2)'));
            })
            ->select('shipments.id as shipment_id','shipments.id as shId', 'shipments.shipper_status_id', 'shipments.tracking_number as tracking_number', 'shipments.tracking_number as tracking','u.name as shipper', 'oc.hub_id as origin_hub_id', 'oc.name as origin', 'dc.hub_id as destination_hub_id', 'dc.name as destination','shipments.order_id','h.name as hub','shipments.consignee_name','shipments.consignee_phone_number_1 as phone','shipments.consignee_address','shipments.amount','sm.mode','bt.booking_type as service_type','shipments_journey.created_at as status_date','shipments_journey.created_at as last_status_date','sj.created_at as arrival', 'shipments.booking_type_id', 'usi.poc')
            ->where('shipments.shipper_status_id',60);

        if (session('role_id') != 1) {
            $shipments = $shipments->where(function($query) {
                $query->where(function ($sub_query){
                    $sub_query->whereIn('dc.hub_id', session('hubs'));
                })
                    ->orWhere(function ($sub_query){
                        $sub_query->whereIn('oc.hub_id', session('hubs'));
                    });
            });
        }

        $datatables = Datatables::of($shipments)
            ->editColumn('amount', function($shipment){
                return number_format($shipment->amount);
            })
            ->editColumn('tracking_number',function ($shipments){
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
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
            ->editColumn('arrival',function($shipments){
                if($shipments->arrival){
                    return $shipments->arrival;
                }else{
                    return " - ";
                }
            });

        return $datatables->make(true);
    }

    public function return_undelivered_print(Request $request){


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

                      /*table.table-bordered {
                        page-break-inside: avoid;
                      }*/

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
            $shipment_ids = ReturnNoteShipment::where('return_note_id',$request->id)->select('shipment_id')->get();
            $filtered_shipments = Shipment::whereIn('id',$shipment_ids)->orderBy('id')->get();
            $filtered_shipments_users = Shipment::whereIn('id',$shipment_ids)->orderBy('id')->groupBy('user_id')->get();
            $shipment_details = '';

            $shipment_details .= '<div class="page text-center">';
            $shipment_details .= '
                          <table class="table table-sm table-bordered border mt-1">
                            <tbody>
                                <tr>
                                    <td class="color primary" colspan="7"><strong style="font-size: large">SUMMARY</strong></td>
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
                $total_users++;
                $user_shipment_collection_charges[$filtered_shipments_user->user_id] = 0;
                $user_total_shipments[$filtered_shipments_user->user_id] = 0;
                foreach ($filtered_shipments as $shipment) {
                    if($shipment->user_id == $filtered_shipments_user->user_id){
                        $user_total_shipments[$filtered_shipments_user->user_id]++;
                    }
                }
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
            $shipment_details .= '
                        </tbody>
                      </table>
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
                    if($shipment->user_id == $filtered_shipments_user->user_id && !in_array($shipment->shipper_status_id, [25,31,38])){
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
                                <td>' . $shipment->items->sum('quantity') . '</td>
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
                            <td class="text-center align-middle color primary"><strong>Return Note</strong></td>
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
                            <td class="color secondary"><strong>Total Undelivered Shipments</strong></td>
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

    public function assign_agent(Request $request){
        $shipment_ids = $request->shipment_ids;
        if($shipment_ids){
            foreach ($shipment_ids as $shipment_id){
                $check_already_assigned = ReturnAssignedShipments::where('shipment_id', $shipment_id)->where('status', 1)->first();
                if($check_already_assigned){
                    $check_already_assigned->status = 0;
                    $check_already_assigned->save();

                            $return_assign_log = new ReturnAssignedShipmentLogs();
                            $return_assign_log->return_assign_shipment_id = $check_already_assigned->id;
                            $return_assign_log->status = 4;
                            $return_assign_log->assigned_by = Auth::id();
                            $return_assign_log->save();
                }
                $assign_shipments = new ReturnAssignedShipments();
                $assign_shipments->admin_id = $request->admin_id;
                $assign_shipments->shipment_id = $shipment_id;
                $assign_shipments->status = 1;
                $assign_shipments->assigned_by = Auth::id();
                $assign_shipments->save();

                $return_assign_log = new ReturnAssignedShipmentLogs();
                            $return_assign_log->return_assign_shipment_id = $assign_shipments->id;
                            $return_assign_log->status = 0;
                            $return_assign_log->assigned_by = Auth::id();
                            $return_assign_log->save();

                //set record in login/logut table
                $check_agent_return_confrimation = AgentReturnConfirmation::where('admin_id',$request->admin_id)->where('current_date',Carbon::now()->format("Y-m-d"));
                
                if(!$check_agent_return_confrimation->exists()){

                 
                    $agent_role = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')
                     ->where('admin_roles.department_id',3)->where('a.id',$request->admin_id)->where('a.status',1);
                     
                     if($agent_role->exists()){
                         $agent_return_confrimation = new AgentReturnConfirmation;
                         $agent_return_confrimation->admin_id = $request->admin_id;
                         $agent_return_confrimation->return_assigned_shipment_id = $assign_shipments->id;
                         $agent_return_confrimation->current_date = Carbon::now()->format("Y-m-d");
                         $agent_return_confrimation->save();
                     }
                 }
                 else{
                    $check_agent_return_confrimation = $check_agent_return_confrimation->get()->first();
                    $check_agent_return_confrimation->return_assigned_shipment_id = $assign_shipments->id;
                    $check_agent_return_confrimation->save();
                 }
                //set record in login/logut table end
                
            }
            return response()->json(['status' => 0, 'success' => 'Shipments Assigned successfully']);
        }
        else{
            return response()->json(['status' => 1, 'error' => 'No Shipment found!']);
        }
    }

    public function assign_agent_excel(Request $request){
        $names = [
            'tracking_number' => 'Tracking Number',
            'agent_id' => 'Agent ID'
        ];
        $messages = [
            'required' => ':attribute is Required.',
            'integer' => ':attribute must be an Integer.',
            'digits_between' => ':attribute must be between :min and :max Digits.',
            'unique' => ':attribute is already Present.'
        ];
        $rules_with_agent = [
            'tracking_number' => ['required', 'integer'],
            'agent_id' => ['required', 'integer']
        ];

        $rules_without_agent = [
            'tracking_number' => ['required', 'integer']
        ];
        $fields = [0 => 'tracking_number', 1 => 'agent_id'];

        if($file = $request->file('shipments')) {
            $spreadsheet = IOFactory::createReaderForFile($file);
            $spreadsheet->setReadDataOnly(true);
            $spreadsheet = $spreadsheet->load($file)->getActiveSheet()->toArray();

            $header = ['Tracking Number', 'Agent ID'];
        }
        if (isset($spreadsheet)) {
            $header_correct = TRUE;

            foreach ($spreadsheet[0] as $index => $header_value) {
                if($index == 1){
                }
                elseif (!isset($header[$index]) || $header_value != $header[$index]) {
                    $header_correct = FALSE;
                    break;
                }
            }

            if (!$header_correct) {
                return redirect()->back()->with('error', 'Invalid Columns, Kindly follow the Template provided');
            }
            else {
                unset($spreadsheet[0]);
            }
        }
        if (!empty($spreadsheet) || !isset($spreadsheet)) {
            $rows = array();
            foreach ($spreadsheet as $spreadsheet_row) {
                $row = array();

                foreach ($spreadsheet_row as $key => $value) {
                    $row[$fields[$key]] = $value;
                }

                $rows[] = $row;
            }

            unset($spreadsheet);
            $errors = array();
            $tracking_ids = array();
            $tracking_id_row = array();
            foreach ($rows as $key => $row) {
                $row_id = $key + 2;
                if(!empty($row['agent_id'])) {
                    $validate = Validator::make($row, $rules_with_agent, $messages);
                }else{
                    $validate = Validator::make($row, $rules_without_agent, $messages);
                }

                $validate->setAttributeNames($names);

                if ($validate->fails()) {
                    $errors['Row #' . $row_id] = $validate->errors()->all();
                }
                if (empty($errors['Row #' . $row_id])) {
                    if (!empty(trim($row['tracking_number']))) {
                        if (empty($tracking_ids)) {
                            $tracking_ids[] = $row['tracking_number'];
                            $tracking_id_row[$row['tracking_number']] = $row_id;
                        }
                        else {
                            if (in_array($row['tracking_number'], $tracking_ids)) {
                                $errors['Row #' . $row_id][] = 'Same Tracking Number as of Row #' . $tracking_id_row[$row['tracking_number']];
                            }
                            else {
                                $tracking_ids[] = $row['tracking_number'];
                                $tracking_id_row[$row['tracking_number']] = $row_id;
                            }
                        }
                    }
                    if (!Shipment::where('tracking_number', $row['tracking_number'])->whereIn('shipper_status_id', [12, 52])->exists()) {
                        $errors['Row #' . $row_id][] = 'Shipment is not valid #' . $row['tracking_number'];
                    }
                    if(!empty($row['agent_id'])) { //if agent = 1 or 2 or 3
                        if (!AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')->where('admin_roles.department_id', 3)->where('a.status', 1)->where('a.id', $row['agent_id'])->exists()) {
                            $errors['Row #' . $row_id][] = 'Agent ID is not valid #' . $row['agent_id']; //remove for ticket no 4934
                        }
                    }
                }
            }
            if(empty($errors)){
                $tracking_numbers = array();
                foreach ($rows as $key => $row) {
                    $row_id = $key + 2;
                    $shipment_id = trim($row['tracking_number']); //111
                    $agent_id = trim($row['agent_id']); //22

                    $id_shipment = Shipment::where('tracking_number', $shipment_id)->first();
                    $check_already_assigned = ReturnAssignedShipments::where('shipment_id', $id_shipment->id)->where('status', 1)->first();
                    if($check_already_assigned){
                        $check_already_assigned->status = 0;
                        $check_already_assigned->save();
                                $return_assign_log = new ReturnAssignedShipmentLogs();
                                $return_assign_log->return_assign_shipment_id = $check_already_assigned->id;
                                $return_assign_log->status = 4;
                                $return_assign_log->assigned_by = Auth::id();
                                $return_assign_log->save();
                    }
                    $assign_shipments = new ReturnAssignedShipments();
                    $assign_shipments->admin_id = $agent_id;
                    $assign_shipments->shipment_id =  $id_shipment->id;
                    $assign_shipments->status = !empty($agent_id) ? 1 : 0;
                    $assign_shipments->assigned_by = Auth::id();
                    $assign_shipments->save();

                    $return_assign_log = new ReturnAssignedShipmentLogs();
                    $return_assign_log->return_assign_shipment_id = $assign_shipments->id;
                    $return_assign_log->status = !empty($agent_id) ? 0 : 4;
                    $return_assign_log->assigned_by = Auth::id();
                    $return_assign_log->save();


                    //if agent is !empty

                    if(!empty($agent_id)){
                        $check_agent_return_confrimation = AgentReturnConfirmation::where('admin_id',$agent_id)->whereDate('current_date',Carbon::now()->format("Y-m-d"));

                        if(!$check_agent_return_confrimation->exists()){
                            $agent_role = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')
                                ->where('admin_roles.department_id',3)->where('a.id',$agent_id)->where('a.status',1);

                            if($agent_role->exists()){
                                $agent_return_confrimation = new AgentReturnConfirmation;
                                $agent_return_confrimation->admin_id = $agent_id;
                                $agent_return_confrimation->return_assigned_shipment_id = $assign_shipments->id;
                                $agent_return_confrimation->current_date = Carbon::now()->format("Y-m-d");
                                $agent_return_confrimation->save();
                            }
                        }
                        else{
                            $check_agent_return_confrimation = $check_agent_return_confrimation->get()->first();
                            $check_agent_return_confrimation->return_assigned_shipment_id = $assign_shipments->id;
                            $check_agent_return_confrimation->save();
                        }

                    }else {
                        //if agent data is empty
                        $check_agent_return_confrimation = AgentReturnConfirmation::
                                where('return_assigned_shipment_id', $assign_shipments->id)
                                    ->orderby('current_date','desc')->first();
                        if(!empty($check_agent_return_confrimation)) {
                            $check_agent_return_confrimation->return_assigned_shipment_id = $assign_shipments->id;
                            $check_agent_return_confrimation->save();
                        }

                    }
                    //set record in login/logut table end

                    $tracking_numbers['Row #' . $row_id] = $shipment_id;

                }
                $tracking_numbers = implode(' | ', array_map(function ($row, $tracking_number) {
                    return $row . ': ' . $tracking_number;
                }, array_keys($tracking_numbers), $tracking_numbers));

                return redirect()->back()->with(['success' => 'Total ' . count($rows) . ' Shipment(s) Updated with Tracking Number(s):' . PHP_EOL . $tracking_numbers]);
            }
            else{
                $errors = array_map(function ($row, $errors) {
                    return $row . ':' . PHP_EOL . implode(' | ', $errors);
                }, array_keys($errors), $errors);

                return redirect()->back()->withErrors($errors);
            }

        }
        else {
            return redirect()->back()->with('error', 'No Shipments in File');
        }
    }

    public function history_get_images(Request $request){
        $return_note_id = $request->return_note_id;
                
        if($return_note_id){
            $shipper_name = ReturnNoteShipment::join('shipments as s', 'return_note_shipments.shipment_id', '=', 's.id')
            ->join('users as u','u.id','=','s.user_id')
            ->where('return_note_shipments.return_note_id',$return_note_id)
            ->select('u.id as id','u.name as name')->distinct()->get();
            
            // SELECT DISTINCT u.name FROM return_note_shipments rs, shipments s,users u WHERE rs.shipment_id=s.id AND u.id=s.user_id  AND rs.return_note_id=66

            $return = ReturnNote::find($return_note_id);
            
            $return_note_shipment = ReturnNoteShipment::where('return_note_id',$return->id)->select('shipment_id')->distinct()->get();//Xyedth
            $shipment=Shipment::whereIn('id',$return_note_shipment)->select('user_id')->get();//Xyedth

            if($return){
                if($return->image !== null){
                    $details = array();
                    $url = 'uploads/return_notes/' . $return->image;
                    if(file_exists($url)){
                        $img_url = asset('uploads/return_notes/' . $return->image);
                    }else{
                        $exists = Storage::disk('s3')->exists('return_note_images/'.$return->image);
                        if($exists){
                            $img_url = Storage::disk('s3')->temporaryUrl('return_note_images/'.$return->image, now()->addMinutes(5));
                        }
                    }

                    $details[] = array('id' => 0,'date' => Carbon::parse($return->updated_at)->toDateTimeString(),'image'=> $img_url);
                    return response()->json(['status' => 0, 'images' => $details]);
                }
                $return_note_images = ReturnNoteImage::where('return_note_id', $return->id);
                if($return_note_images->exists()){
                    $return_note_images = $return_note_images->get();
                    $details = array();
                    foreach ($return_note_images as $index => $return_note_image) {
                        if($return_note_image->user_id != null){
                            $user = User::find($return_note_image->user_id);

                            $return_note_shipments = ReturnNoteShipment::join('shipments as s','return_note_shipments.shipment_id', '=', 's.id')
                                ->where('return_note_shipments.return_note_id',$return_note_id)
                                ->where('s.user_id',$user->id)->count();
                            $url = 'uploads/return_notes/' . $return_note_image->image;
                            if(file_exists($url)){
                                $img_url = asset('uploads/return_notes/' . $return_note_image->image);
                            }else{
                                $exists = Storage::disk('public')->exists('uploads/return_notes/'.$return_note_image->image);
                                if($exists){
                                    $img_url = asset('storage/uploads/return_notes/'.$return_note_image->image);
                                }
                                else{
                                    $exists = Storage::disk('s3')->exists('return_note_images/'.$return_note_image->image);
                                    if($exists){
                                        $img_url = Storage::disk('s3')->temporaryUrl('return_note_images/'.$return_note_image->image, now()->addMinutes(5));
                                    }
                                }

                            }

                            if(!array_key_exists($return_note_image->user_id, $details)){
                                $details[$return_note_image->user_id] = array('shipperid'=>$return_note_image->user_id, 'id' => $return_note_image->id,'shipper'=>$user->name,'noOfshipment'=>$return_note_shipments,'date' => Carbon::parse($return_note_image->created_at)->toDateTimeString());
                            }

                            $details[$return_note_image->user_id]['images'][] = $img_url;
                        }
                        else{
                            $url = 'uploads/return_notes/' . $return_note_image->image;
                            if(file_exists($url)){
                                $img_url = asset('uploads/return_notes/' . $return_note_image->image);
                            }else{
                                $exists = Storage::disk('public')->exists('uploads/return_notes/'.$return_note_image->image);
                                if($exists){
                                    $img_url = asset('storage/uploads/return_notes/'.$return_note_image->image);
                                }
                                else{
                                    $exists = Storage::disk('s3')->exists('return_note_images/'.$return_note_image->image);
                                    if($exists){
                                        $img_url = Storage::disk('s3')->temporaryUrl('return_note_images/'.$return_note_image->image, now()->addMinutes(5));
                                    }
                                }

                            }
                            $details[] = array('id' => $return_note_image->id,'date' => Carbon::parse($return_note_image->created_at)->toDateTimeString(),'image'=> $img_url);
                        }
                    }
                    
                    return response()->json(['status' => 0, 'details' => $details,'shippers'=> $shipper_name]);
                }else{
                    return response()->json(['status' => 2,'shippers'=> $shipper_name]);
                }
                return response()->json(['status' => 1, 'error' => 'Return Note Images not found!']);
            }
            return response()->json(['status' => 1, 'error' => 'Return Note ID not found!']);
        }
        return response()->json(['status' => 1, 'error' => 'Return Note ID not selected, please try again!']);
    }
    
    public function history_delete_image(Request $request){
        $return_note_id = $request->return_note_id;
        $return_note_image_id = $request->return_note_image_id;
        $return_user_id = $request->user_id;
        if($return_note_image_id == 0){
            $return_note = ReturnNote::find($return_note_id);
            if($return_note){
                $return_note->image = NULL;
                $return_note->save();
                return response()->json(['status' => 0, 'success' => 'Image deleted successfully!']);
            }
            return response()->json(['status' => 1, 'error' => 'Return Note not found!']);
        }else{
            if ($return_user_id == 'undefined') {
                ReturnNoteImage::where('id', $return_note_image_id)->delete();
                return response()->json(['status' => 0, 'success' => 'Image deleted successfully!']);
            }
            else
            {
                ReturnNoteImage::where('return_note_id', $return_note_id)->where('user_id',$return_user_id)->delete();
                return response()->json(['status' => 0, 'success' => 'Image deleted successfully!']);    
            }
        }
        return response()->json(['status' => 1, 'error' => 'Image not found!']);
    }

    public function history_delete_lastimage(Request $request){
        $return_note_id = $request->return_note_id;
        $return_note_image_id = $request->return_note_image_id;
        $return_user_id = $request->user_id;
        $return_note = ReturnNote::find($return_note_id);
        if($return_note_image_id == 0){
            if($return_note){
                $return_note->image = NULL;
                // $return_note->updated_by = Auth::id();
                // $return_note->status = 3;
                $return_note->save();
                return response()->json(['status' => 0, 'success' => 'Image deleted and status updated successfully!']);
            }
            return response()->json(['status' => 1, 'error' => 'Return Note not found!']);
        }else{
            if ($return_user_id == 'undefined') {
                ReturnNoteImage::where('id', $return_note_image_id)->delete();
                $return_note->updated_by = Auth::id();
                $return_note->status = 3;
                $return_note->save();
                return response()->json(['status' => 0, 'success' => 'Image deleted and status updated successfully!']);
            }
            else
            {
                ReturnNoteImage::where('return_note_id', $return_note_id)->where('user_id',$return_user_id)->delete();
                $return_note->updated_by = Auth::id();
                $return_note->status = 3;
                $return_note->save();
                return response()->json(['status' => 0, 'success' => 'Image deleted and status updated successfully!']);    
            }
        }
        return response()->json(['status' => 1, 'error' => 'Image not found!']);
    }

    public function return_deliveries_index()
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(), 416);
        return view('admin.return.rider_return_deliveries');
    }

    public function return_deliveries_list(Request $request)
    {
        if ($request->get('excel') && $request->get('excel') == true) {
            ActivityTrailController::createActivityTrailLog(Auth::id(), 417);
        }
        $return_deliveries = ReturnNote::join('cities as c', 'return_notes.hub_id', '=', 'c.id')
            ->join('zones as z', 'z.id', '=', 'c.zone_id')
            ->join('riders as r', 'return_notes.rider_id', '=', 'r.id')
            ->select('return_notes.id as return_note_id', 'z.name as zone', 'return_notes.created_at as created_at', 'r.name as rider', 'return_notes.shipments_count as total_shipments', 'c.name as city', DB::raw('(SELECT COUNT(shipment_id) as id FROM `return_note_shipments` AS `adns` where `adns`.`return_note_id` = `return_notes`.`id` AND `adns`.`update_type` = 1) AS `shipments_rider_updated`'), DB::raw('(SELECT COUNT(shipment_id) as id FROM `return_note_shipments` AS `dns` where `dns`.`return_note_id` = `return_notes`.`id` AND `dns`.`update_type` = 0 AND `dns`.`status` > 0) AS `shipments_dbf_updated`'));


        $datatable = Datatables::of($return_deliveries)
            ->addColumn('return_note', function ($return_deliveries) {
                return '<button class="btn btn-sm btn-outline-info align-middle print"><i class="la la-lg la-print align-middle"></i> <span class="align-middle">' . str_pad($return_deliveries->return_note_id, 6, '0', STR_PAD_LEFT) . '</span></button>';
            })
            ->addColumn('return_note_id_padded', function ($return_deliveries) {
                return str_pad($return_deliveries->return_note_id, 6, '0', STR_PAD_LEFT);
            })
            ->editColumn('total_shipments_link', function ($return_deliveries) {
                if ($return_deliveries->total_shipments != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $return_deliveries->total_shipments . '</button>';
                } else {
                    return 0;
                }
            })
            ->editColumn('shipments_rider_updated', function ($return_deliveries) {
                if ($return_deliveries->shipments_rider_updated != null) {
                    return $return_deliveries->shipments_rider_updated;
                } else {
                    return 0;
                }
            })
            ->addColumn('update_via_app', function ($return_deliveries) {
                if ($return_deliveries->shipments_rider_updated != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $return_deliveries->shipments_rider_updated . '</button>';
                } else {
                    return 0;
                }
            })
            ->addColumn('update_via_dbf', function ($return_deliveries) {
                $count = $return_deliveries->shipments_dbf_updated;
                if ($count != 0) {
                    return '<button class="btn btn-sm btn-outline-info align-middle">' . $count . '</button>';
                } else {
                    return 0;
                }
            });
        return $datatable->make(true);
    }

    public function return_deliveries_app_shipments_list(Request $request)
    {
        $return_note_id = $request->return_note_id;

        $shipments = ReturnNoteShipment::join('shipments as s', 's.id', '=', 'return_note_shipments.shipment_id')
            ->join('rider_return_deliveries', function ($join) {
                $join->on('return_note_shipments.shipment_id', '=', 'rider_return_deliveries.shipment_id')
                    ->where('rider_return_deliveries.id', '=',
                        DB::raw('(select max(id) from rider_return_deliveries as rrd where rrd.shipment_id = return_note_shipments.shipment_id AND rrd.return_note_id = return_note_shipments.return_note_id)'));
            })
            ->leftjoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'return_note_shipments.shipment_id')
                    ->where('shipments_journey.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = return_note_shipments.shipment_id and reference_1_id = return_note_shipments.return_note_id and shipments_journey.shipper_status_id != 5 and rider_id is not null)'));
            })
            ->leftjoin('shipment_status as ss', 'ss.id', '=', 'shipments_journey.shipper_status_id')
            ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'shipments_journey.status_reason_id')
            ->select('s.id as shipment_id', 's.tracking_number', 'shipments_journey.shipper_status_id', 'ss.name as shipment_status', 'ssr.name as shipment_reason', 'shipments_journey.created_at as update_date_time', 'shipments_journey.received_or_refused_by', 'rider_return_deliveries.picture_path', 'rider_return_deliveries.pod_image', 'rider_return_deliveries.delivered_status', 'rider_return_deliveries.audio_path')
            ->where('return_note_shipments.update_type', 1)
            ->where('return_note_shipments.return_note_id', $return_note_id);


        $datatables = Datatables::of($shipments)
            ->addColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->addColumn('status', function ($shipments) {
                if ($shipments->delivered_status == 1) {
                    return 'Return - Delivered to Shipper';
                } else {
                    return 'Return - Delivery Unsuccessful';
                }
            })
            ->addColumn('pod', function ($shipments) {
                $image = '';
                if ($shipments->picture_path != null) {
                    $exists = Storage::disk('public')->exists($shipments->picture_path);
                    if ($exists) {
                        $image .= '<div class="text-center"><button type="button" class="btn btn-primary btn-sm picture" data-link="' . asset(Storage::url($shipments->picture_path)) . '"><i class="la la-image"></i> View</button></div>';
                    } else {
                        $img = Storage::disk('s3')->temporaryUrl($shipments->picture_path, now()->addMinutes(5));
                        $image = '<a class="btn btn-sm btn-outline-info align-middle" href="' . $img . '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';
                    }

                    return $image;
                } else {
                    return '-';
                }
            })
            ->addColumn('pod_image', function ($shipments) {
                $image = '';
                if ($shipments->pod_image != null) {
                    $exists = Storage::disk('public')->exists($shipments->pod_image);
                    if ($exists) {
                        $image .= '<div class="text-center"><button type="button" class="btn btn-primary btn-sm picture" data-link="' . asset(Storage::url($shipments->pod_image)) . '"><i class="la la-image"></i> View</button></div>';
                    } else {
                        $img = Storage::disk('s3')->temporaryUrl($shipments->pod_image, now()->addMinutes(5));
                        $image = '<a class="btn btn-sm btn-outline-info align-middle" href="' . $img . '" target="_blank"><i class="la la-lg la-image align-middle"></i> <span class="align-middle">View</span></a>';
                    }

                    return $image;
                } else {
                    return '-';
                }
            })
            ->editColumn('audio_path', function ($shipments) {
                $audio = '';
                if ($shipments->audio_path != null) {
                    $audio .= '<div class="text-center"><button type="button" class="btn btn-primary btn-sm audio" data-link="' . asset(Storage::url($shipments->audio_path)) . '"><i class="la la-file-sound-o"></i> Listen</button></div>';

                    return $audio;
                } else {
                    return '-';
                }
            });
        return $datatables->make(true);
    }

    public function return_deliveries_dbf_shipments_list(Request $request)
    {
        $return_note_id = $request->return_note_id;

        $shipments = ReturnNoteShipment::join('shipments as s', 's.id', '=', 'return_note_shipments.shipment_id')
            ->leftjoin('shipments_journey', function ($join) {
                $join->on('shipments_journey.shipment_id', '=', 'return_note_shipments.shipment_id')
                    ->where('shipments_journey.id', '=',
                        DB::raw('(select max(id) from shipments_journey where shipments_journey.shipment_id = return_note_shipments.shipment_id and reference_1_id = return_note_shipments.return_note_id and shipments_journey.shipper_status_id != 5 and rider_id is null)'));
            })
            ->leftjoin('shipment_status as ss', 'ss.id', '=', 'shipments_journey.shipper_status_id')
            ->leftJoin('shipment_status_reason as ssr', 'ssr.id', '=', 'shipments_journey.status_reason_id')
            ->select('s.id as shipment_id', 's.tracking_number', 'shipments_journey.shipper_status_id', 'ss.name as shipment_status', 'ssr.name as shipment_reason', 'shipments_journey.created_at as update_date_time', 'shipments_journey.received_or_refused_by')
            ->where('return_note_shipments.update_type', 0)
            ->where('return_note_shipments.status', '>', 0)
            ->where('return_note_shipments.return_note_id', $return_note_id);
        $datatables = Datatables::of($shipments)
            ->addColumn('tracking_number_link', function ($shipments) {
                $route = route('admin.tracking.index');
                return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
            })
            ->addColumn('status', function ($shipments) {
                if ($shipments->shipper_status_id == 25) {
                    return 'Return - Delivered to Shipper';
                } else {
                    return 'Return - Delivery Unsuccessful';
                }
            });
        return $datatables->make(true);

    }

    public function rcp_agent_index(){
        ActivityTrailController::createActivityTrailLog(Auth::id(),451);
        $today = Carbon::now()->endOfDay();
        $agents = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')
            ->where('admin_roles.department_id',3)->get();
        $hubs = City::where([['status',1],['hub',1]])->get();
        $shippers = User::select('id','name')->get();
        return view('admin.return.rcp_agent', compact('agents','today','hubs','shippers'));
    }

    public function rcp_agent_list(Request $request){   
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),452);
        }
        
        $agent_productivity = AgentReturnConfirmation::join('admins as a','a.id','=','agent_return_confirmations.admin_id')
                    ->join('return_assigned_shipments as ras','agent_return_confirmations.return_assigned_shipment_id','=','ras.id')
                    ->select('a.name as agent_name','a.id as agent_id','agent_return_confirmations.login_time as start_time','agent_return_confirmations.logout_time as end_time','agent_return_confirmations.current_date', 'agent_return_confirmations.admin_id');
        $datatable = Datatables::of($agent_productivity)
        ->addColumn('total_assigning', function ($agent_productivity){
            $total_assigning = ReturnAssignedShipments::join('return_assigned_shipment_logs as rasl','rasl.return_assign_shipment_id','=','return_assigned_shipments.id')
            ->where('return_assigned_shipments.admin_id',$agent_productivity->agent_id)
            ->where('rasl.status',0)
            ->whereDate('rasl.created_at',$agent_productivity->current_date)->count();
            return $total_assigning;

        })
        ->addColumn('actual_productivity', function ($agent_productivity){
           
            $actual_productivity = ReturnAssignedShipments::join('return_assigned_shipment_logs as rasl','rasl.return_assign_shipment_id','=','return_assigned_shipments.id')
            ->where('return_assigned_shipments.admin_id',$agent_productivity->agent_id)
            ->whereIn('rasl.status',[1,2,3,7])
            ->whereDate('rasl.created_at',$agent_productivity->current_date)->count();
          
            return $actual_productivity;
         })
         ->addColumn('reattempt', function ($agent_productivity){
           

            $reattempt = ReturnAssignedShipments::join('return_assigned_shipment_logs as rasl','rasl.return_assign_shipment_id','=','return_assigned_shipments.id')
            ->where('return_assigned_shipments.admin_id',$agent_productivity->agent_id)
            ->where('rasl.status',1)
            ->whereDate('rasl.created_at',$agent_productivity->current_date)->count();
          
            return $reattempt;
         })
         ->addColumn('return', function ($agent_productivity){

            $return = ReturnAssignedShipments::join('return_assigned_shipment_logs as rasl','rasl.return_assign_shipment_id','=','return_assigned_shipments.id')
            ->where('return_assigned_shipments.admin_id',$agent_productivity->agent_id)
            ->where('rasl.status',2)
            ->whereDate('rasl.created_at',$agent_productivity->current_date)->count();
          
            return $return;
        })
         ->addColumn('intercept', function ($agent_productivity){

            $intercept = ReturnAssignedShipments::join('return_assigned_shipment_logs as rasl','rasl.return_assign_shipment_id','=','return_assigned_shipments.id')
            ->where('return_assigned_shipments.admin_id',$agent_productivity->agent_id)
            ->where('rasl.status',3)
            ->whereDate('rasl.created_at',$agent_productivity->current_date)->count();
          
            return $intercept;
         })
         ->addColumn('pending', function ($agent_productivity){
            
            $total_assigning = ReturnAssignedShipments::join('return_assigned_shipment_logs as rasl','rasl.return_assign_shipment_id','=','return_assigned_shipments.id')
            ->where('return_assigned_shipments.admin_id',$agent_productivity->agent_id)
            ->where('rasl.status',0)
            ->whereDate('rasl.created_at',$agent_productivity->current_date)->count();
        
            $actual_productivity = ReturnAssignedShipments::join('return_assigned_shipment_logs as rasl','rasl.return_assign_shipment_id','=','return_assigned_shipments.id')
            ->where('return_assigned_shipments.admin_id',$agent_productivity->agent_id)
            ->whereIn('rasl.status',[1,2,3,7])
            ->whereDate('rasl.created_at',$agent_productivity->current_date)->count();
                
            if($total_assigning == 0){
                return '0';
            }else{

                return $total_assigning - $actual_productivity;
            }
         })
         ->addColumn('productivity', function ($agent_productivity){
                
                $total_assigning = ReturnAssignedShipments::join('return_assigned_shipment_logs as rasl','rasl.return_assign_shipment_id','=','return_assigned_shipments.id')
                ->where('return_assigned_shipments.admin_id',$agent_productivity->agent_id)
                ->where('rasl.status',0)
                ->whereDate('rasl.created_at',$agent_productivity->current_date)->count();
            
                $actual_productivity = ReturnAssignedShipments::join('return_assigned_shipment_logs as rasl','rasl.return_assign_shipment_id','=','return_assigned_shipments.id')
                ->where('return_assigned_shipments.admin_id',$agent_productivity->agent_id)
                ->whereIn('rasl.status',[1,2,3,7])
                ->whereDate('rasl.created_at',$agent_productivity->current_date)->count();

                if($total_assigning == 0){
                    return 0;
                }else{
                    return number_format(($actual_productivity/($total_assigning))*100,2);
                }
         })
         ->addColumn('un_assigned', function ($agent_productivity){
        
            $un_assigned = ReturnAssignedShipments::join('return_assigned_shipment_logs as rasl','rasl.return_assign_shipment_id','=','return_assigned_shipments.id')
                ->where('return_assigned_shipments.admin_id',$agent_productivity->agent_id)
                ->where('rasl.status',4)
                ->whereDate('rasl.created_at',$agent_productivity->current_date)->count();
            
                return $un_assigned;

         })->addColumn('on_hold_for_sc', function ($agent_productivity){

            $intercept = ReturnAssignedShipments::join('return_assigned_shipment_logs as rasl','rasl.return_assign_shipment_id','=','return_assigned_shipments.id')
            ->where('return_assigned_shipments.admin_id',$agent_productivity->agent_id)
            ->where('rasl.status',7)
            ->whereDate('rasl.created_at',$agent_productivity->current_date)->count();

            return $intercept;
         });
            
           
            //  ->addColumn('un_assigned', function ($agent_productivity){
            
            //     $un_assigned = ReturnAssignedShipments::join('return_assigned_shipment_logs as rasl','rasl.return_assign_shipment_id','=','return_assigned_shipments.id')
            //         ->where('return_assigned_shipments.admin_id',$agent_productivity->agent_id)
            //         ->where('rasl.status',4)
            //         ->whereDate('rasl.created_at',$agent_productivity->current_date)->count();
                
            //         return $un_assigned;

            //  });
             if ($request->get('from_date') && $request->get('to_date')) {
                $from = $request->get('from_date');
                $to = $request->get('to_date');
                $agent_productivity = $agent_productivity->whereBetween('agent_return_confirmations.current_date',[$from,$to]);
            }
            if ($request->get('agent')) {
                $agent_ids = $request->get('agent');
                $agent_productivity = $agent_productivity->whereIn('agent_return_confirmations.admin_id',$agent_ids);
            }
            if($request->get('hub')){
                $hub_id = $request->get('hub');
               
                $agent_productivity->join('shipments as shhub','shhub.id','=','ras.shipment_id')
                ->join('cities AS dc', 'shhub.consignee_city_id', '=', 'dc.id')
                ->join('cities as h' ,'dc.hub_id', '=' , 'h.id')
                ->where('h.id',$hub_id);

            }
            
            return $datatable->make(true);
    }

    public function return_revert_index(Request $request)
    {
        ActivityTrailController::createActivityTrailLog(Auth::id(),475);
        $settings = GlobalSettings::where('type', 'global_rider_id')->first();

        if ($settings) {
            $global_rider_id = $settings->setting_value;
        } else {
            $global_rider_id = 0;
        }
        $riders = Rider::where('status', 1)->select('id', 'name')->get();
        return view('admin.return.revert')->with(['riders' => $riders, 'global_rider_id' => $global_rider_id]);
    }
    public function return_revert_shipment_details(Request $request)
    {
        $shipment = Shipment::where('tracking_number', $request->tracking_number)->whereIn('shipper_status_id', [25,31,38])->first();
        if($shipment)
        {
            $details = array();

            $return_note_id = ReturnNoteShipment::where('shipment_id', $shipment->id)->orderBy('return_note_id', 'desc')->first();

            $details['id'] = $shipment->id;
            $details['tracking_number'] = $shipment->tracking_number;
            $details['shipper'] = $shipment->user->name;
            $details['return_note'] = $return_note_id->return_note_id;

            ShipmentScanningJourneyController::add($shipment->id, 29, 1, Auth::id(), null,null);
            
            return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
        } 
        else {
            return ['status' => 1, 'error' => 'Given Tracking Number\'s Shipment is on another status'];
        }
    }
    public function return_revert_submit(Request $request)
    {
        $shipment_ids = explode(',', $request->shipment_ids);

        foreach ($shipment_ids as $key => $shipment_id) {
            $shipment = Shipment::find($shipment_id);
            if($shipment)
            {
                $return_note_id = ReturnNoteShipment::where('shipment_id', $shipment->id)->orderBy('return_note_id', 'desc')->first();
                $shipment->shipper_status_id = 47;
                $shipment->save();
                ShipmentsJourneyController::add($shipment_id, 47, 47, null, null, null, Auth::id());

                $return_revert_log = new ReturnRevertLog;
                $return_revert_log->return_note = $return_note_id->return_note_id;
                $return_revert_log->shipment_id = $shipment->id;
                $return_revert_log->shipper = $shipment->user->name;
                $return_revert_log->updated_by = Auth::id();
                $return_revert_log->save();
            }
        }
        return redirect()->back()->with(['success' => 'Shipments Reverted']);
    }

    public function update_estimatecharges($shipment, $charge)
    {
        $shipment_id = $shipment;
        $charges = $charge;
        if($shipment_id){
            if($charges != null){
                $consolidated_shipments = ConsolidationShipments::where('shipment_id', $shipment_id);
                if($consolidated_shipments->exists()) {
                    $consolidated_shipments = $consolidated_shipments->first();
                    $all_consolidation_shipments = ConsolidationShipments::where('consolidation_id', $consolidated_shipments->consolidation_id)->pluck('shipment_id')->toArray();
                    Shipment::whereIn('id', $all_consolidation_shipments)->update(['nsa_osa_estimated_charges' => $charges]);
                }else{
                    $shipment = Shipment::find($shipment_id);
                    $shipment->nsa_osa_estimated_charges = $charges;
                    $shipment->save();
                }
                $this->add_osa_charges($shipment_id,$charges);

                return 0;
            }else{
                return 1;
            }

        }else{
            return 1;
        }
    }
    public function add_osa_charges($shipment, $charge)//function to add in logs table
    {
        $nsa_charges_log = new OsaChargesLog();
        $nsa_charges_log->shipment_id = $shipment;
        $nsa_charges_log->osa_charges = $charge;
        $nsa_charges_log->updated_by = Auth::id();
        $nsa_charges_log->save();
    }

    public function confirmation_pending_sms_index(){
       ActivityTrailController::createActivityTrailLog(Auth::id(),502);
       return view('admin.return.confirmation_pending_sms');
    }

    public function confirmation_pending_sms_list(Request $request){
        if($request->get('excel') && $request->get('excel') == true)
        {
            ActivityTrailController::createActivityTrailLog(Auth::id(),503);
        }
       $rcp = ReturnConfirmationPendingSmsAttempt::join('shipments as s','s.id','=','return_confirmation_pending_sms_attempts.shipment_id')
           ->select('s.id as shipment_id','s.tracking_number as tracking_number','s.tracking_number as tracking','s.consignee_phone_number_1 as phone','return_confirmation_pending_sms_attempts.response as response','return_confirmation_pending_sms_attempts.created_at as created_at','return_confirmation_pending_sms_attempts.updated_at as updated_at','return_confirmation_pending_sms_attempts.status as status','return_confirmation_pending_sms_attempts.count as count');

       $datatable = DataTables::of($rcp)
           ->editColumn('tracking_number', function ($shipments) {
               $route = route('admin.tracking.index');
               return "<u><a href='{$route}?tracking_number=$shipments->tracking_number' class='tracking' target='_blank'>$shipments->tracking_number</a></u>";
           })
       ->editColumn('updated_at',function($rcp){
            if($rcp->status == 0){
                return '-';
            }
            else{
                return $rcp->updated_at;
            }
       })
       ->editColumn('status',function ($rcp){
            if ($rcp->status == 0) {
                return 'Pending';
            }
            else if ($rcp->status == 1) {
                return 'Return';
            }
            else if ($rcp->status == 2) {
                return 'Re-Attempt';
            }
            else if ($rcp->status == 3) {
                return 'Attempt Limit Reached';
            }
            else {
                return 'Invalid Number';
            }
       })
       ->filterColumn('return_confirmation_pending_sms_attempts.status',function ($query,$keyword){

           $keyword = strtolower($keyword);
           if ($keyword == 'pending') {
               $query->where('return_confirmation_pending_sms_attempts.status',0);
           }
           else if($keyword == 'return') {
               $query->where('return_confirmation_pending_sms_attempts.status',1);
           }
           else if($keyword == 're-attempt') {
               $query->where('return_confirmation_pending_sms_attempts.status',2);
           }
           else if($keyword == 'attempt limit reached') {
               $query->where('return_confirmation_pending_sms_attempts.status',3);
           }
           else if($keyword == 'invalid number') {
               $query->where('return_confirmation_pending_sms_attempts.status',4);
           }
           else {
               $query->whereRaw('false');
           }
       });

        return $datatable->make(true);
    }

    public function manual_rcp_sms(Request $request){
        if($request->id){
            $shipment = Shipment::find($request->id)->id;
            if($shipment){
                $rcp_sms = ReturnConfirmationPendingSmsAttempt::where('status',0)->where('shipment_id', $shipment);
                if($rcp_sms->exists()){
                    $rcp_sms = $rcp_sms->latest('id')->first();
                    $limit = GlobalSettings::where('type','return_confirmation_pending_sms')->first();

                    if ($rcp_sms->count <= $limit->text){
                        dispatch(new RCPSmsToConsignee($rcp_sms->shipment_id));
                    }
                }
                else{
                    return response()->json(['status' => 1, 'error' => 'Shipment not found in SMS attempts!']);
                }
            }
            else{
                return response()->json(['status' => 1, 'error' => 'Shipment not found!']);
            }
        }
    }
}
