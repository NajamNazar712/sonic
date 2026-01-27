<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Controllers\Admins\AdminCRMController;
use App\Http\Controllers\CRM\CRMCommentController;
use App\Http\Controllers\CRM\CRMController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\ChangeShipmentAmountLog;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\Admin\Retail\RetailUser;
use App\Http\Models\CRM\CrmComments;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\CRM\CrmRequestChannel;
use App\Http\Models\CRM\CrmRequestFeedback;
use App\Http\Models\CRM\CrmRequestRating;
use App\Http\Models\CRM\CrmRequestStatus;
use App\Http\Models\CRM\CrmRequestStatusHistory;
use App\Http\Models\DonePayment;
use App\Http\Models\DonePaymentShipment;
use App\Http\Models\ReceivingSheet;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentItem;
use App\Http\Models\ShipmentStatus;
use App\Http\Models\Shipper\SubstituteUser;
use App\Http\Models\Shipper\User;
use App\Http\Models\V2Pickup\V2PickupRequestShipment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ShipmentsJourneyController;
use App\Http\Models\CRM\CrmClosedReasonStatus;
use App\Http\Models\Sister_account\MergedSisterAccount;
use App\Http\Models\Sister_account\MergedSisterAccountMapping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use App\Http\Models\PendingPayment;


class ShipperCRMController extends Controller
{
    public function __construct() {
        $this->middleware('auth:web,substitute_users');

        $this->middleware('Permission');
    }

    public function index(){
        $case_nature = CrmRequestCaseNature::all(['id', 'name']);
        $case_nature_type = CrmRequestCaseNatureType::select('id', 'type')->where('status_id',1)->get();
        $channels = CrmRequestChannel::select('id', 'channel')->get();
        $status = CrmRequestStatus::where('id', '!=', 3)->select('id', 'name')->get();
        $shipment_status = ShipmentStatus::select('id', 'name')->get();

        $masp = [session('user_id')];
        $merged_account_sister_mapping = MergedSisterAccountMapping::where('head_user_id',session('user_id'))->pluck('sister_user_id')->toArray();
        
        if(count($merged_account_sister_mapping) >  0){
            $masp = array_merge($masp,$merged_account_sister_mapping);
        }

        $launched = CrmRequest::where('status_id',1)
        ->whereIn('shipper_id', $masp)
        ->count();
        
        $in_process = CrmRequest::where('status_id',2)
        ->whereIn('shipper_id', $masp)
        ->count();
        
        $closed = CrmRequest::where('status_id',4)
        ->whereIn('shipper_id', $masp)
        ->count();

        $closed_reason_statuses  = CrmClosedReasonStatus::all();
        $merged_accounts = [];
        $get_merged_head_id = MergedSisterAccount::where('user_id', session('user_id'))->first();
        if($get_merged_head_id){
            $merged_head_id =  $get_merged_head_id->merged_head_id;

            $merged_accounts = MergedSisterAccount::leftjoin('users as u', 'u.id', '=', 'merged_sister_accounts.user_id')
                ->leftjoin('cities as c', 'c.id', '=', 'u.city_id')
                ->select('u.id as id', 'u.name as name', 'u.poc as poc', 'u.phone as phone', 'u.address as address', 'c.name as city')
                ->where('merged_head_id', $merged_head_id)
                ->get();
        }
        return view('client.crm.requests')->with(['case_nature' => $case_nature, 'case_nature_type' => $case_nature_type, 'channels' => $channels, 'status' => $status, 'shipment_status' => $shipment_status, 'launched' => $launched, 'in_process' => $in_process, 'closed' => $closed, 'closed_reason_statuses' => $closed_reason_statuses, 'merged_accounts' => $merged_accounts]);
    }
    public function requests_list(Request $request){

        $masp = [session('user_id')];
        $merged_account_sister_mapping = MergedSisterAccountMapping::where('head_user_id',session('user_id'))->pluck('sister_user_id')->toArray();
        
        if(count($merged_account_sister_mapping) >  0){
            $masp = array_merge($masp,$merged_account_sister_mapping);
        }
        
        $launched_request = CrmRequest::leftjoin('crm_request_case_nature as crcn', 'crcn.id', '=', 'crm_requests.case_nature_id')
            ->leftjoin('crm_request_case_nature_types as crcnt', 'crcnt.id', '=', 'crm_requests.case_nature_type_id')
            ->leftjoin('crm_request_channels as crc', 'crc.id', '=', 'crm_requests.channel_id')
            ->leftjoin('crm_request_statuses as crs', 'crs.id', '=', 'crm_requests.status_id')
            ->leftjoin('admins as ad', 'ad.id', '=', 'crm_requests.agent_id')
            ->leftjoin('admins as a', 'a.id', '=', 'crm_requests.launched_by_id')
            ->leftjoin('shipments as s', 's.id', '=', 'crm_requests.shipment_id')
            ->leftjoin('shipment_status as ss', 'ss.id', '=', 's.shipper_status_id')
            ->leftjoin('crm_closed_reasons as crmcr', 'crmcr.crm_request_id', '=', 'crm_requests.id')
            ->leftjoin('crm_closed_reason_statuses as crmcrs', 'crmcrs.id', '=', 'crmcr.status_id')
            ->leftjoin('crm_request_status_histories as crmst', function ($join) {
                $join->on('crmst.crm_request_id', '=', 'crm_requests.id')
                ->where('crm_requests.status_id', 4)
                ->where('crmst.id', '=',
                DB::raw('(select max(id) from crm_request_status_histories where crm_request_status_histories.crm_request_id = crm_requests.id)'));
            })
            ->leftjoin('users as u', 'u.id', '=', 'crm_requests.shipper_id')
            ->select('crm_requests.id as id', 's.tracking_number as tracking_number', 'crcn.name as case_nature', 'crcnt.type as case_nature_type', 'crc.channel as channel', 'crs.name as request_status', 'ad.name as agent', 'a.name as name', 'crm_requests.launched_by as launched_added_by', 'crm_requests.created_at as created_at','crm_requests.description','crm_requests.status_id', 'ss.name as shipment_status','crm_requests.description as descr','crmst.created_at as closed_at','crm_requests.launched_by_id','crmcrs.name as at_fault', 'u.name as shipper_name')
            ->whereIn('crm_requests.shipper_id', $masp);
            
            if ($request->get('search_date_from') && $request->get('search_date_to')) {
                $from = $request->get('search_date_from');
                $to = $request->get('search_date_to');
                $launched_request = $launched_request->whereBetween('crm_requests.created_at', [$from, $to]);
            }
            if(isset($request->search_account_type) && count($request->search_account_type) > 0){
                $launched_request->whereIn('crm_requests.shipper_id', $request->search_account_type);
            }
        $datatables = Datatables::of($launched_request)
            ->addColumn('id_padded', function ($requests) {
                return str_pad($requests->id, 6, '0', STR_PAD_LEFT);
            })
            
            ->editColumn('descr',function($request){
                return strip_tags($request->description);
            })
            ->editColumn('at_fault',function($request){
                if($request->status_id == 4){
                    return $request->at_fault;
                }else{
                    return '-';
                }
            })
            
            ->addColumn('id_padded_link', function ($requests) {
                return '<u><a href=' . route('cod.crm.request.details', ['id' => $requests->id]) . ' target="_blank">' . str_pad($requests->id, 6, '0', STR_PAD_LEFT). '</a></u>';
            })
            ->addColumn('tracking_number_hyperlink', function ($requests) {
                return '<u><a href=' . route('cod.tracking.index') . '?tracking_number=' . $requests->tracking_number . ' class="tracking" target="_blank">' . $requests->tracking_number . '</a></u>';
            })
            ->addColumn('launched_by_name', function ($requests) {
                if($requests->launched_added_by == 0){
                    $launched_by = Admin::find($requests->launched_by_id);
                    if($launched_by){
                        return $launched_by->name;
                    }
                }else if($requests->launched_added_by == 1){
                    $launched_by = User::find($requests->launched_by_id);
                    if($launched_by){
                        return $launched_by->name;
                    }
                }else if($requests->launched_added_by == 2){
                    $launched_by = SubstituteUser::find($requests->launched_by_id);
                    if($launched_by){
                        return $launched_by->name;
                    }
                }else if($requests->launched_added_by == 3){
                    $launched_by = RetailUser::find($requests->launched_by_id);
                    if($launched_by){
                        return $launched_by->name;
                    }
                }
                    return '-';
                
                
            })
            ->editColumn('agent', function ($requests){
                if($requests->agent == null){
                    return '-';
                }
                else{
                    return $requests->agent;
                }
            })
            ->editColumn('added_by', function($requests){
                if($requests->launched_added_by == 0) {
                    return 'Admin';
                }
                else if($requests->launched_added_by == 1) {
                    return 'Shipper';
                }
                else{
                    return 'Shipper Substitute User';
                }
            })
            ->addColumn('status',function ($requests){
                if($requests->status_id == 3){
                    return 'In-Process';
                }else{
                    return $requests->request_status;
                }
            })
            ->filterColumn('status',function ($query,$keyword){

                if ($keyword != '') {
                    if ($keyword == 2) {
                        $query->whereIn('crm_requests.status_id',[2,3]);
                    }
                    else {
                        $query->where('crm_requests.status_id', '=', $keyword);
                    }
                }
            })
            ->filterColumn('shipment_status',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('ss.id',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->filterColumn('case_nature_type',function ($query,$keyword){

                if ($keyword != '') {
                    $query->where('crcnt.id','=',$keyword);
                }
                else {
                    $query->whereRaw('false');
                }
            })
            ->addColumn('action', function($requests) {
                $route = route('cod.crm.request.details', ['id' => $requests->id]);
                    $dropdown = '
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
                    <div class="dropdown-menu dropdown-menu-sm">
                        <button onclick="window.open(\'' . $route . '\', \'_tab\')" type="button" class="dropdown-item"><div class="row no-gutters align-items-center"><div class="col-2"><i class="ft-plus-circle"></i></div><div class="col-9 offset-1">View Details</div></button>
                        
                    </div>
                  </div>
                ';

                    return $dropdown;

            })->rawColumns(['action','tracking_number_hyperlink','id_padded_link']);

        return $datatables->make(true);
    }

    public function request_details(Request $request, $id){
        $crm_request = CrmRequest::find($id);
        if($crm_request->shipper_id == session('user_id')){
            $shipment_status = null;
            if($crm_request->shipment_id != null) {
                $shipment_status = Shipment::find($crm_request->shipment_id);
                $shipment_status = $shipment_status?->status_shipper?->name;
            }
            $crm_comments = array();
            $last_comment = null;
            $crm_comments = CrmComments::where('crm_request_id', $id)->where('comment_type',0);
            if($crm_comments->exists()){
                $crm_comments = $crm_comments->orderBy('created_at','asc')->get();
                $last_comment = CrmComments::where('crm_request_id', $id)->latest()->first();
                $last_comment = $last_comment->id;
            }
            $launched_by  = '';
            if($crm_request->launched_by == 0){
                $launched_by = 'Agent';
            }else if($crm_request->launched_by == 1){
                $launched_by = User::find($crm_request->launched_by_id)->name;
            }else if($crm_request->launched_by == 2){
                $launched_by = SubstituteUser::find($crm_request->launched_by_id)->name;
            }else if($crm_request->launched_by == 3){
                $launched_by = RetailUser::find($crm_request->launched_by_id)->name;
            }
            if($crm_request){
                $settings = GlobalSettings::where('type', 'crm_reopen_count')->first();
                if($settings->text == "on") {
                    $reopen_count = (int)$crm_request->reopen_count;
                    $setting_value = (int)$settings->setting_value;
                    if ($reopen_count < $setting_value) {
                        $reopen_check = true;
                    }
                    else {
                        $reopen_check = false;
                    }
                }
                else{
                    $reopen_check = true;
                }

                $product_insurance = ShipmentItem::where('shipment_id',$crm_request->shipment_id);
                if($product_insurance->exists()){
                    $product = $product_insurance->first();
                    $insurance_check = $product->insurance;
                    if($insurance_check == 1){
                        $insurance = 'Yes';
                    }
                    else{
                        $insurance = 'No';
                    }
                }
                else{
                    $insurance = 'No';
                }

                $ratings = CrmRequestRating::all();

                if($crm_request->status_id == 4){
                    $give_feedback = TRUE;
                    $feedback = CrmRequestFeedback::where('crm_request_id', $crm_request->id);
                    if($feedback->exists()){
                        $feedback = $feedback->first();
                        if($feedback->reopen == $crm_request->reopen_count){
                            $give_feedback = false;
                        }
                    }
                }
                else{
                    $give_feedback = FALSE;
                }


                return view('client.crm.details')->with(['crm_details' => $crm_request, 'launched_by' => $launched_by, 'comments' => $crm_comments, 'last_comment_id' => $last_comment, 'shipment_status' => $shipment_status, 'reopen_check' => $reopen_check,'insurance' => $insurance, 'ratings' => $ratings, 'give_feedback' => $give_feedback]);
            }else{
                return redirect()->back()->with('danger', 'CRM Request Not found!');
            }
        }else{
            return redirect()->route('cod.crm.request.index')->with('error', '404 Not Found!');
        }
    }

    public function add_request(Request $request){
        $nature_id = $request->case_nature_id;
        $complaint_id = $request->complaint_id;
        $shipment_ids = $request->shipment_ids;
        $shipment_id = $request->shipment_id;
        $receiving_sheet_id = $request->receiving_sheet_id;
        $alternate_phone = null; // default
        if($request->has('alternate_phone')){
            if($request->alternate_phone){
                $alternate_phone = $request->alternate_phone;
            }
        }

        $is_automated_cod_change = false;
        if($request->has('is_automated_cod_change')){
            if($request->is_automated_cod_change){
                $is_automated_cod_change = true;
            }else{
                $is_automated_cod_change = false;
            }
        }

        // if($complaint_id == 23 && $receiving_sheet_id != null){
        if($complaint_id == 23){
            $description_text = $request->description ;
            // $description = '<strong>' .'Receiving Sheet No: ' .$receiving_sheet_id. '</strong>'. PHP_EOL. $description_text;
            $description = $description_text;
        }
        else{
            $description = $request->description;
        }

        $launched_by = 1;
        if(!$request->case_nature_id){
            return ['status' => 0, 'error' => 'Case nature not selected!'];
        }
        $present_shipments = array();
        $flag = false;
        $cannot_change = false;
        if(session('user_type') == 2){
            $launched_by = 2;
        }
        if ($request->has('payment_request')) {
            if($request->payment_request == 1){
                $payment_id = $request->payment_id;
                $payment_id_padded = str_pad($request->payment_id, 6, 0, STR_PAD_LEFT);
                if(!empty($payment_id)){
                    $payment = DonePayment::find($payment_id);
                    $payment_shipment = DonePaymentShipment::where('done_payment_id', $payment->id)->first();
                    $shipment = Shipment::where('id', $payment_shipment->shipment_id)->first();
                    $is_shipment = CrmRequest::where('shipment_id',$shipment->id)->where('case_nature_id', $nature_id);

                    if($is_shipment->exists()){
                        return ['status' => 0, 'error' => 'Request/Complaint already lodged for the Payment ID: ' . $payment_id_padded];
                    }
                    else{
                        $check_claim_can_lock = CrmRequest::where('shipment_id',$shipment->id)->latest()->first();
                        $response = AdminCRMController::canLockClaim($shipment, $is_shipment, $nature_id, $request, $check_claim_can_lock);

                        if ($response['status'] === 0) {
                            return $response;
                        }

                        $canComplaintPaymentLocked = AdminCRMController::canComplaintPaymentLocked($shipment->id);
                        if ($canComplaintPaymentLocked['status'] === 0) {
                            return $canComplaintPaymentLocked;
                        }
                        
                        CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment->id, session('user_id'), NULL, $description);
                    }
                    return ['status' => 1, 'success' => 'Request(s) successfully added'];
                }else{
                    return ['status' => 0, 'error' => 'No Payment selected!'];
                }
            }
        }
        elseif ($request->has('pickup_request')) {
            if($request->pickup_request == 1){
                $pickup_request_ids = $request->pickup_request_ids;
                if ($request->hasFile('product_picture') && $request->hasFile('invoice_picture')) {
                    $pickup_request_ids = explode(',', $request->input('pickup_request_ids'));
                }
                else{
                    if($complaint_id == 26){
                        $pickup_request_ids = explode(',', $request->input('pickup_request_ids'));
                    }
                }
                if(!empty($pickup_request_ids)){
                    foreach ($pickup_request_ids as $pickup_request_id){
                        $pickup_request_shipment = V2PickupRequestShipment::where('pickup_request_id', $pickup_request_id)->first();
                        $shipment = Shipment::where('id', $pickup_request_shipment->shipment_id)->first();
                        $shipment_id = $shipment->id;
                        $is_shipment = CrmRequest::where('shipment_id',$shipment->id)->where('case_nature_id', $nature_id)->first();
                        $check_claim_can_lock = CrmRequest::where('shipment_id',$shipment->id)->latest()->first();
                        $response = AdminCRMController::canLockClaim($shipment, $is_shipment, $nature_id, $request, $check_claim_can_lock);

                        if ($response['status'] === 0) {
                            return $response;
                        }
                        if($nature_id == 1 && $request->complaint_id == 1){
                            $canComplaintPaymentLocked = AdminCRMController::canComplaintPaymentLocked($shipment_id);
                            if ($canComplaintPaymentLocked['status'] === 0) {
                                return $canComplaintPaymentLocked;
                            }
                        }

                        if($is_shipment){

                            if($is_shipment->case_nature_id != $nature_id){
                                if ($request->hasFile('product_picture') && $request->hasFile('invoice_picture')) {

                                    CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL , NULL, $request->product_cost ?: $request->claim_product_cost,  $request->file('product_picture'), $request->file('invoice_picture'));
                                }
                                else{

                                    CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description);
                                }
                            }else{

                                $present_shipments[] = $shipment->tracking_number;
                                $flag = true;
                            }
                        }else{
                            if ($request->hasFile('product_picture') && $request->hasFile('invoice_picture')) {
                                CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL , NULL, $request->product_cost ?: $request->claim_product_cost,  $request->file('product_picture'), $request->file('invoice_picture'));
                            }
                            else{
                                CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description);
                            }
                        }
                    }
                    return ['status' => 1, 'success' => 'Request(s) successfully added', 'flag' => $flag, 'already_existed_shipments' => $present_shipments];
                }else{
                    return ['status' => 0, 'error' => 'No Pickup Request selected!'];
                }
            }
        }

        else{
            if ($request->hasFile('product_picture') && $request->hasFile('invoice_picture')) {
                $shipment_ids = explode(',', $request->input('shipment_ids'));
            }
            else{
                if($complaint_id == 26){
                    $shipment_ids = explode(',', $request->input('shipment_ids'));
                }
            }
            $message = "Request(s) successfully added";
            if(!empty($shipment_ids)){
                foreach ($shipment_ids as $shipment_id) {
                    $shipment = Shipment::find($shipment_id);
                    if($shipment){

                        if($request->cod_new_amount == 0 && $complaint_id == 12 && PendingPayment::negative_payable_check($shipment->user_id, $shipment->user->account_type_id)) {
                            return ['status'=> 0 , 'error' => 'You are unable to change the amount due to negative balance.'];
                        }

                        if($complaint_id == 12 && in_array($shipment->shipper_status_id, [14, 18, 30, 36, 37, 20, 21, 22, 23, 24, 25, 26, 32, 44, 47, 48, 57, 60, 51])) // for cod change automation
                        {
                            return ['status' => 0, 'error' => 'Request cannot be catered at this status of the shipment.'];
                        }

                        $is_shipment = CrmRequest::where('shipment_id',$shipment_id)->where('case_nature_id',$nature_id)->first();
                        $check_claim_can_lock = CrmRequest::where('shipment_id',$shipment_id)->latest()->first();

                        $response = AdminCRMController::canLockClaim($shipment, $is_shipment, $nature_id, $request, $check_claim_can_lock);

                        if ($response['status'] === 0) {
                            return $response;
                        }

                        if($nature_id == 1 && $request->complaint_id == 1){
                            $canComplaintPaymentLocked = AdminCRMController::canComplaintPaymentLocked($shipment_id);
                            if ($canComplaintPaymentLocked['status'] === 0) {
                                return $canComplaintPaymentLocked;
                            }
                        }

                        $already_lodged = false;
                        if($is_shipment){ 
                            $already_lodged = true;
                            $request_check = true;

                            if($nature_id == 1) {
                                $check_request = CrmRequest::where('shipment_id',$shipment_id)->where('case_nature_id',$nature_id)
                                    ->orderBy('id', 'desc')
                                    ->first();
                                if($check_request && $check_request->status_id!=4) {
                                    $request_check = false;
                                }
                            }

                            if($is_shipment->case_nature_id != $nature_id  || $request_check){
                                if ($request->hasFile('product_picture') && $request->hasFile('invoice_picture')) {
                                    CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL , NULL, $request->product_cost ?: $request->claim_product_cost,  $request->file('product_picture'), $request->file('invoice_picture'));
                                }
                                else{
                                    if($shipment->shipper_status_id == 20 || $shipment->shipper_status_id == 1){
                                        if(in_array($complaint_id, [11, 13])){
                                            $present_shipments[] = $shipment->tracking_number;
                                            $flag = true;
                                            $cannot_change = true;
                                        }
                                        else{
                                            
                                            CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description);
                                        }
                                    }
                                    // auto change service type
//                                    else if($complaint_id == 39 && in_array($shipment->shipper_status_id,[53,2,3,4,5,12,65,66,21,56])) {
//                                        $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, $shipment->user_id, NULL, $description, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, null,true);
//                                    }
                                    else{
                                        CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description);
                                    }
                                }
                            }else{

                                $present_shipments[] = $shipment->tracking_number;
                                $flag = true;
                            }
                        }else{

                            if ($request->hasFile('product_picture') && $request->hasFile('invoice_picture')) {
                                if($nature_id == 4) {
                                    if ($complaint_id == 21 || $complaint_id == 22) {
                                        CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description, $request->product_cost ?: $request->claim_product_cost, $request->file('product_picture'), $request->file('invoice_picture'), $request->file('damage_product_picture'), $request->file('product_packaging_picture'), $request->file('actual_product_picture'), $request->damage_claim_product_cost, $request->file('missing_product_picture'), $request->file('product_packaging_picture_content_short'), $request->file('actual_product_picture_content_short'), $request->claim_content_product_cost);
                                    } else {
                                        CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description, $request->product_cost ?: $request->claim_product_cost, $request->file('product_picture'), $request->file('invoice_picture'), null, null, null, null, null, null, null, null);
                                    }
                                }
                                else{
                                        CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, NULL, $request->product_cost ?: $request->claim_product_cost, $request->file('product_picture'), $request->file('invoice_picture'));
                                    }
                                }
                            else{
                                if($shipment->shipper_status_id == 20 || $shipment->shipper_status_id == 1){
                                    if(in_array($complaint_id, [11, 13])){
                                        $present_shipments[] = $shipment->tracking_number;
                                        $flag = true;
                                        $cannot_change = true;
                                    }
                                    else{
                                        if($complaint_id == 12 && $is_automated_cod_change)
                                        {
                                            if($shipment->shipper_status_id == 5)
                                            {
                                                $description = $description. " (change old amouunt $shipment->amount to new amount $request->cod_new_amount )";
                                                CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description);
                                                
                                            }
                                            else
                                            {
                                                CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, $is_automated_cod_change);
                                            }
                                        }
                                        else{
                                            $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description);
                                        }
                                    }
                                }
                                else{
                                    if($complaint_id == 12 && $is_automated_cod_change)
                                    {
                                        if($shipment->shipper_status_id == 5)
                                        {
                                            $description = $description. " (change old amouunt $shipment->amount to new amount $request->cod_new_amount )";
                                            CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description);
                                            
                                        }
                                        else
                                        {
                                            CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, $is_automated_cod_change);
                                        }
                                    }
                                    // auto change service type
//                                    else if($complaint_id == 39 && in_array($shipment->shipper_status_id,[53,2,3,4,5,12,65,66,21,56])) {
//                                        $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, $shipment->user_id, NULL, $description, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, null,true);
//                                    }
                                    else{
                                        $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description);
                                    }
                                }
                            }
                        }

                        if($nature_id == 2){
                            if($complaint_id == 13){
                                $shipment->consignee_phone_number_2 = $alternate_phone;
                                $shipment->save();
                            }
                            else if($complaint_id == 12 && !$already_lodged && $is_automated_cod_change) // for cod change automation
                            {
                                if($request->has('cod_new_amount')){
                                    if($request->cod_new_amount >= 0 && $shipment->shipper_status_id != 5){

                                        $crm_request_id = CrmRequest::where('shipment_id', $shipment->id)->pluck('id')->first();
                                        $default_agent_id = 306;
                                        $comment_by = 0;
                                        $comment_type = 0;
                                        $comment = "Dear Customer,
                                                    Request of “COD Change” from (Old amount: $shipment->amount) to (New amount: $request->cod_new_amount) has been updated on system
                                                    
                                                    CRM automated Comment";
                                        
                                        CRMCommentController::add($crm_request_id, $default_agent_id, $comment_by, $comment_type, $comment,1);
                                        
                                        $old_amount = $shipment->amount;
                                        $message = "Request of “COD Change” from (Old amount: $shipment->amount) to (New amount: $request->cod_new_amount) has been updated on system";
                                        $shipment->amount = $request->cod_new_amount;
                                        if($request->is_zero_cod == 1  && $request->cod_parcel_value > 0)
                                        {
                                            $comment = "Dear Customer,
                                            Request of “COD Change” from (Old amount: $shipment->amount) to (New amount: $request->cod_new_amount) has been updated on system
                                            Due to change of COD amount 0. parcel value has been updated from ($shipment->parcel_value) to ($request->cod_parcel_value)
                                            
                                            CRM automated Comment";
                                            $shipment->parcel_value = $request->cod_parcel_value;
                                        }
                                        ChangeShipmentAmountLog::create([
                                            'shipment_id' => $shipment->id,
                                            'old_amount' => $old_amount,
                                            'new_amount' => $request->cod_new_amount,
                                            'remarks' => $request->cod_remarks,
                                            'admin_id' => 346 // for global admin
                                        ]);
                                        $shipment->save();
                                    }
                                }
                            }

                            
                            // else if($complaint_id == 12){
                            //     if($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 2 || $shipment->shipper_status_id == 3 || $shipment->shipper_status_id == 4 || $shipment->shipper_status_id == 8 || $shipment->shipper_status_id == 7 || $shipment->shipper_status_id == 13 || $shipment->shipper_status_id == 52 || $shipment->shipper_status_id == 12){
                            //         if($shipment->amount != 0){
                            //            if($shipment->retail){
                            //             if($cod_amount > $shipment->amount){
                            //                 $shipment->amount = $cod_amount;
                            //             }
                            //            }else{
                            //             $shipment->amount = $cod_amount;
                            //            }
                            //            $shipment->save(); 
                            //         }
                            //         if($shipment->shipper_status_id == 52 || $shipment->shipper_status_id == 12){
                            //         //mark reattempt

                            //             $shipment->shipper_status_id = 13;
                            //             $shipment->consignee_status_id = 13;
                            //             $shipment->save();
                            //             ShipmentsJourneyController::add($shipment->id, 13, 13, NULL, NULL, NULL, 346);
                            //         }
                            //     }
                            // }
                            else if($complaint_id == 32){
                                $shipment->special_instructions = 'Allow to Open Shipment';
                                $shipment->save();
                            }
                        }
                    }

                }
                if($nature_id == 1){
                    AdminCRMController::updateComplaintPhone($crm_request_padded_id ?? CrmRequest::max('id'), $request->case_nature_complainant, $request->complainant_phone);
                }

                return ['status' => 1, 'success' => $message, 'flag' => $flag, 'already_existed_shipments' => $present_shipments, 'cannot_change' => $cannot_change];
//            return ['status' => 1, 'success' => 'Request(s) successfully added'];
            }
            else if (!empty($shipment_id)){
                $shipment = Shipment::find($shipment_id);
                if($shipment){
                    if($request->cod_new_amount == 0 && $complaint_id == 12 && PendingPayment::negative_payable_check($shipment->user_id, $shipment->user->account_type_id)) {
                        return ['status'=> 0 , 'error' => 'You are unable to change the amount due to negative balance.'];
                    }

                    if($complaint_id == 12 && in_array($shipment->shipper_status_id, [14, 18, 30, 36, 37, 20, 21, 22, 23, 24, 25, 26, 32, 44, 47, 48, 57, 60, 51])) // for cod change automation
                    {
                        return ['status' => 0, 'error' => 'Request cannot be catered at this status of the shipment.'];
                    }

                    $is_shipment = CrmRequest::where('shipment_id',$shipment_id)->where('case_nature_id',$nature_id)->first();
                    $check_claim_can_lock = CrmRequest::where('shipment_id',$shipment_id)->latest()->first();
                    $response = AdminCRMController::canLockClaim($shipment, $is_shipment, $nature_id, $request, $check_claim_can_lock);

                    if ($response['status'] === 0) {
                        return $response;
                    }

                    if($nature_id == 1 && $request->complaint_id == 1){
                        $canComplaintPaymentLocked = AdminCRMController::canComplaintPaymentLocked($shipment_id);
                        if ($canComplaintPaymentLocked['status'] === 0) {
                            return $canComplaintPaymentLocked;
                        }
                    }  
                    $already_lodged = false;
                    if($is_shipment){  
                        $already_lodged = true;
                        $request_check = true;

                        if($nature_id == 1) {
                            $check_request = CrmRequest::where('shipment_id',$shipment_id)->where('case_nature_id',$nature_id)
                                ->orderBy('id', 'desc')
                                ->first();
                            if($check_request && $check_request->status_id!=4) {
                                $request_check = false;
                            }
                        }

                        if($is_shipment->case_nature_id != $nature_id || $request_check){
                            if($shipment->shipper_status_id == 20 || $shipment->shipper_status_id == 1){
                                if(in_array($complaint_id, [11, 13])){
                                    $present_shipments[] = $shipment->tracking_number;
                                    $flag = true;
                                    $cannot_change = true;
                                }
                                else{
                                    if($complaint_id == 12 && $is_automated_cod_change)
                                    {
                                        if($shipment->shipper_status_id == 5)
                                        {
                                            $description = $description. " (change old amouunt $shipment->amount to new amount $request->cod_new_amount )";
                                            CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description);
                                            
                                        }
                                        else
                                        {
                                            CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, $is_automated_cod_change);
                                        }
                                    }
                                    else{
                                        $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description);
                                    }
                                }
                            }
                            else{
                                if($complaint_id == 12 && $is_automated_cod_change)
                                {
                                    if($shipment->shipper_status_id == 5)
                                    {
                                        $description = $description. " (change old amouunt $shipment->amount to new amount $request->cod_new_amount )";
                                        CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description);
                                        
                                    }
                                    else
                                    {
                                        CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, $is_automated_cod_change);
                                    }
                                }
                                // auto change service type
//                                else if($complaint_id == 39 && in_array($shipment->shipper_status_id,[53,2,3,4,5,12,65,66,21,56])) {
//                                    $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, $shipment->user_id, NULL, $description, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, null,true);
//                                }
                                else{
                                    $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description);
                                }
                            }
                        }else{
                            $present_shipments[] = $shipment->tracking_number;
                            $flag = true;
                        }
                    }else{
                        
                        
                        if($shipment->shipper_status_id == 20 || $shipment->shipper_status_id == 1){
                            if(in_array($complaint_id, [11, 13])){
                                $present_shipments[] = $shipment->tracking_number;
                                $flag = true;
                                $cannot_change = true;
                            }
                            else{
                                
                                if($complaint_id == 12 && $is_automated_cod_change)
                                {
                                    if($shipment->shipper_status_id == 5)
                                    {
                                        $description = $description. " (change old amouunt $shipment->amount to new amount $request->cod_new_amount )";
                                        CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description);
                                        
                                    }
                                    else
                                    {
                                        CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, $is_automated_cod_change);
                                    }
                                }
                                else{
                                    $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description);
                                }
                                
                            }
                        }
                        else{
                            if($complaint_id == 12 && $is_automated_cod_change)
                            {
                                if($shipment->shipper_status_id == 5)
                                {
                                    $description = $description. " (change old amouunt $shipment->amount to new amount $request->cod_new_amount )";
                                    CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description); 
                                }
                                else
                                {
                                    CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, $is_automated_cod_change);
                                }
                                
                            }
                            // auto change service type
//                            else if($complaint_id == 39 && in_array($shipment->shipper_status_id,[53,2,3,4,5,12,65,66,21,56])) {
//                                $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, $shipment->user_id, NULL, $description, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, null,true);
//                            }
                            else{
                                $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, 1, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description);
                            }
                        }
                    }
                    
                    if($nature_id == 2){
                        
                        if($complaint_id == 13){
                            $shipment->consignee_phone_number_2 = $alternate_phone;
                            $shipment->save();
                        }
                        else if($complaint_id == 12 && !$already_lodged && $is_automated_cod_change) // for cod change automation
                        {
                            if($request->has('cod_new_amount')){
                                if($request->cod_new_amount >= 0 && $shipment->shipper_status_id != 5){

                                    $crm_request_id = CrmRequest::where('shipment_id', $shipment->id)->pluck('id')->first();
                                    $default_agent_id = 306;
                                    $comment_by = 0;
                                    $comment_type = 0;
                                    $comment = "Dear Customer,
                                                Request of “COD Change” from (Old amount: $shipment->amount) to (New amount: $request->cod_new_amount) has been updated on system";
                                    
                                    CRMCommentController::add($crm_request_id, $default_agent_id, $comment_by, $comment_type, $comment,1);

                                    $old_amount = $shipment->amount;
                                    $message = "Request of “COD Change” from (Old amount: $shipment->amount) to (New amount: $request->cod_new_amount) has been updated on system";
                                    $shipment->amount = $request->cod_new_amount;
                                    if($request->is_zero_cod == 1  && $request->cod_parcel_value > 0)
                                    {
                                        $comment = "Dear Customer,
                                        Request of “COD Change” from (Old amount: $shipment->amount) to (New amount: $request->cod_new_amount) has been updated on system
                                        Due to change of COD amount 0. parcel value has been updated from ($shipment->parcel_value) to ($request->cod_parcel_value)
                                        
                                        CRM automated Comment";
                                        $shipment->parcel_value = $request->cod_parcel_value;
                                    }
                                    ChangeShipmentAmountLog::create([
                                        'shipment_id' => $shipment->id,
                                        'old_amount' => $old_amount,
                                        'new_amount' => $request->cod_new_amount,
                                        'remarks' => $request->cod_remarks,
                                        'admin_id' => 346 // for global admin
                                    ]);
                                    $shipment->save();
                                }
                            }
                            
                        }
                        // else if($complaint_id == 12){
                        //     if($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 2 || $shipment->shipper_status_id == 3 || $shipment->shipper_status_id == 4 || $shipment->shipper_status_id == 8 || $shipment->shipper_status_id == 7 || $shipment->shipper_status_id == 13 || $shipment->shipper_status_id == 52 || $shipment->shipper_status_id == 12){
                        //         if($shipment->amount != 0){
                        //            if($shipment->retail){
                        //             if($cod_amount > $shipment->amount){
                        //                 $shipment->amount = $cod_amount;
                        //             }
                        //            }else{
                        //             $shipment->amount = $cod_amount;
                        //            }
                        //            $shipment->save(); 
                        //         }
                        //         if($shipment->shipper_status_id == 52 || $shipment->shipper_status_id == 12){
                        //         //mark reattempt

                        //             $shipment->shipper_status_id = 13;
                        //             $shipment->consignee_status_id = 13;
                        //             $shipment->save();
                        //             ShipmentsJourneyController::add($shipment->id, 13, 13, NULL, NULL, NULL, 346);
                        //         }
                        //     }
                        // }
                        else if($complaint_id == 32){
                            $shipment->special_instructions = 'Allow to Open Shipment';
                            $shipment->save();
                        }
                    }
                }
                if($nature_id == 1){
                    AdminCRMController::updateComplaintPhone($crm_request_padded_id ?? CrmRequest::max('id'), $request->case_nature_complainant, $request->complainant_phone);
                }
                return ['status' => 1, 'success' => $message, 'flag' => $flag, 'already_existed_shipments' => $present_shipments, 'cannot_change' => $cannot_change];
            }
            else{
                return ['status' => 0, 'error' => 'No shipments selected!'];
            }
        }
    }

    public function add_feedback(Request $request){
        $nature_id = 3;
        $channel_id = 1;
        $description = $request->description;
        $launched_by = 1;
        $shipment_ids = $request->shipment_ids;
        $flag = false;
        $present_shipments = null;
        if($shipment_ids != null) {
            foreach ($shipment_ids as $shipment_id) {
                if (session('user_type') == 2) {
                    $launched_by = 2;
                }
                if ($channel_id == null) {
                    return ['status' => 0, 'error' => 'Channel Not selected!'];
                }
                if ($description == null) {
                    return ['status' => 0, 'error' => 'Description Not Entered!'];
                }
                $shipment = Shipment::find($shipment_id);
                if($shipment) {
                    $is_shipment = CrmRequest::where('shipment_id', $shipment_id)->where('case_nature_id', $nature_id)->first();
                    if (!$is_shipment) {
                        CRMController::add($nature_id, NULL, $channel_id, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description);
                    } else {

                        $present_shipments[] = $shipment->tracking_number;
                        $flag = true;
                    }
                }else{
                    CRMController::add($nature_id, null, $channel_id, 1, Auth::id(), $launched_by, $shipment_id, session('user_id'), NULL, $description);
                }
            }
        }
        else{
            if (session('user_type') == 2) {
                $launched_by = 2;
            }
            if ($channel_id == null) {
                return ['status' => 0, 'error' => 'Channel Not selected!'];
            }
            if ($description == null) {
                return ['status' => 0, 'error' => 'Description Not Entered!'];
            }

            CRMController::add($nature_id, NULL, $channel_id, 1, Auth::id(), $launched_by, null, session('user_id'), NULL, $description);
        }
        return ['status' => 1, 'success' => 'Feedback successfully added', 'flag' => $flag, 'already_existed_shipments' => $present_shipments];
    }

    public function add_comment(Request $request){

        
        $comment = $request->comment;
        $request_id = $request->request_id;
        $comment_by = 1;
        if($comment == null){
            return ['status' => 0, 'error' => 'Comment Not selected!'];
        }
        if(!$request_id){
            return ['status' => 0, 'error' => 'Request ID Not selected!'];
        }
        if(session('user_type') == 2){
            $comment_by = 2;
        }
        
        CRMCommentController::add($request_id, Auth::id(),$comment_by,0, $comment,1, NULL, 1);
        $last_comment = CrmComments::where('crm_request_id', $request_id)->latest()->first();
        return ['status' => 1, 'success' => 'Comment successfully added', 'last_comment_id' => $last_comment->id];

    }

    public function get_latest_comment(Request $request){
        $comment_id = $request->comment_id;
        $request_id = $request->request_id;
        if(($comment_id != null) && ($request_id != null)){
            $comment_details = CrmComments::where('crm_request_id', $request_id)->where('comment_type',0)->latest()->first();
            if($comment_details){
                $name = '';
                if($comment_details->id > $comment_id){
                    if($comment_details->comment_by == 2){
                        $name = $comment_details->substitute_user->name;
                    }else if($comment_details->comment_by == 0){
                        $name = 'Agent';
                    }
                    return ['status' => 1, 'comment' => $comment_details, 'name' => $name];
                }
            }
        }


    }
    public function re_open_request(Request $request){
        $crm_request = CrmRequest::where('id', $request->req_id)->first();
        if ($crm_request->status_id == 4) {
            if ($crm_request['status_id'] != 5) {
                $count = $crm_request->reopen_count + 1;
                CrmRequest::where('id', $request->req_id)->update([
                    'status_id' => 5,
                    'reopen_count' => $count
                ]);
                CrmRequestStatusHistory::create([
                    'crm_request_id' => $request->req_id,
                    'status_id' => 5,
                    'agent_id' => null
                ]);
                return redirect()->back()->with(['success' => 'Request marked as Re-Open']);
            } else {
                return redirect()->back()->with(['error' => 'Request is already marked as Re-Open']);
            }
        }
    }

    public function lost_claim(Request $request){

        $shipment = Shipment::find($request->shipment_id);
        if($shipment){
//            if($shipment->shipper_status_id == 1 || $shipment->shipper_status_id == 17){
                if($shipment->receiving_sheet_shipment){
                    $receiving_sheet_id = $shipment->receiving_sheet_shipment->receiving_sheet_id;
                    return response()->json(['status' => 1,'receiving_sheet_id' => $receiving_sheet_id]);
                }
                else{
                    return response()->json(['status' => 0,'error'=>'Receiving Sheet does not exists']);
                }
//            }
//            else{
//             return response()->json(['status' => 0,'error'=>'Only Booked and Cancelled Shipments Allowed']);
//            }
        }
        return response()->json(['status' => 0,'error'=>'No Shipments Found']);
    }

    public function customer_feedback(Request $request){
        $rating_id = $request->rating_id;
        $request_id = $request->request_id;

        if($request_id && $rating_id){
            $crm_request = CrmRequest::find($request_id);
            if($crm_request && $crm_request->status_id == 4){
                $crm_request_rating = CrmRequestFeedback::where('crm_request_id', $request_id)->where('user_id', session('user_id'));
                if($crm_request_rating->exists()){
                    $crm_request_rating = $crm_request_rating->latest()->first();

                    if($crm_request->reopen_count > $crm_request_rating->reopen){

                        $crm_request_rating->rating_id = $rating_id;
                        $crm_request_rating->reopen = $crm_request->reopen_count;
                        $crm_request_rating->save();
                    }
                    else{
                        return response()->json(['status' => 1, 'message' => 'Feedback already received!']);
                    }
                }
                else{
                    $crm_request_rating = new CrmRequestFeedback();
                    $crm_request_rating->crm_request_id = $request_id;
                    $crm_request_rating->user_id = session('user_id');
                    $crm_request_rating->rating_id = $rating_id;
                    $crm_request_rating->save();
                }
                return response()->json(['status' => 0, 'message' => 'Feedback Received!']);
            }
            return response()->json(['status' => 1, 'message' => 'Request not found!']);

        }
        else{
            return response()->json(['status' => 1, 'message' => 'Something went wrong!']);
        }
    }

    public function card_data(Request $request){
        
        try {
            $masp = [session('user_id')];
            $merged_account_sister_mapping = MergedSisterAccountMapping::where('head_user_id',session('user_id'))->pluck('sister_user_id')->toArray();
            
            if(count($merged_account_sister_mapping) >  0){
                $masp = array_merge($masp,$merged_account_sister_mapping);
            }

            $launched = CrmRequest::where('status_id',1);
            $in_process = CrmRequest::where('status_id',2);
            $closed = CrmRequest::where('status_id',4);
            if(isset($request->search_account_type) && count($request->search_account_type) > 0){
                $launched = $launched->whereIn('shipper_id', $request->search_account_type);
                $in_process = $in_process->whereIn('shipper_id', $request->search_account_type);
                $closed = $closed->whereIn('shipper_id', $request->search_account_type);
            } else {
                $launched = $launched->whereIn('shipper_id', $masp);
                $in_process = $in_process->whereIn('shipper_id', $masp);
                $closed = $closed->whereIn('shipper_id', $masp);
            }

            if ($request->get('from_date') && $request->get('to_date')) {
                $from = $request->get('from_date');
                $to = $request->get('to_date');

                $launched = $launched->whereBetween('created_at', [$from, $to]);
                $in_process = $in_process->whereBetween('created_at', [$from, $to]);
                $closed = $closed->whereBetween('created_at', [$from, $to]);

            }
            

            $launched =  $launched->count();
            $in_process =  $in_process->count();
            $closed =  $closed->count();

            $card_data['launched'] = $launched;
            $card_data['in_process'] = $in_process;
            $card_data['closed'] = $closed;

            return response()->json(['status' => 1, 'card_data' => $card_data]);

        } catch (\Throwable $th) {
            return response()->json(['status' => 0]);
            
        }
     
    }

    public function bulk_claim_index(){
        $case_nature_type = CrmRequestCaseNatureType::where('nature_id',4)->select('id', 'type')->get();
        $channels = CrmRequestChannel::select('id', 'channel')->get();
        return view('client.crm.bulk_claim')->with(['case_nature_type' => $case_nature_type, 'channels' => $channels]);

    }

    public function bulk_claim_shipment_details(Request $request){
        $shipment = Shipment::where('tracking_number', $request->tracking_number)->where('user_id',session('user_id'));

        if ($shipment->exists()) {
            $shipment = $shipment->first();
            $is_shipment = CrmRequest::where('shipment_id',$shipment->id)->where('case_nature_id', 4);
            if($is_shipment->exists()){
                return ['status' => 1, 'error' => 'Request/Complaint already lodged'];
            }

            $details = array();
            $details['id'] = $shipment->id;
            $details['tracking_number'] = $shipment->tracking_number;
            return ['status' => 0, 'success' => 'Shipment has been added', 'details' => $details];
        }else{
            return ['status' => 1, 'error' => 'No Shipment with given Tracking Number is present'];
        }

    }

    public function bulk_claim_submit(Request $request){
        
        $shipment_ids = explode(',', $request->shipment_ids);
        $present_shipments = [];

        foreach ($shipment_ids as $shipment_id) {
            $shipment = Shipment::find($shipment_id);
            if($shipment){
                $is_shipment = CrmRequest::where('shipment_id',$shipment_id)->first();
                $is_shipment = CrmRequest::where('shipment_id',$shipment_id)->where('case_nature_id',4)->first();
                if($is_shipment){
                    $present_shipments[] = $shipment->tracking_number;
                }else{
                    $crm_request_padded_id = CRMController::add(4, $request->case_nature_type_id[$shipment_id], $request->channel_id[$shipment_id], 1, Auth::id(), 1, $shipment_id, $shipment->user_id, NULL , $request->description[$shipment_id], $request->claim_product_cost[$shipment_id],  $request->file('product_picture')[$shipment_id], $request->file('invoice_picture')[$shipment_id]);
                                      
                    if($request->has('key_account')){
                        $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $request->channel_id[$shipment_id], $request->case_nature_type_id[$shipment_id]);
                    }
                    $crm_request_padded_id = str_pad($crm_request_padded_id, 6, 0, STR_PAD_LEFT);
                    // return ['status' => 1, 'success' => 'Request ('. $crm_request_padded_id .') successfully added', 'flag' => $flag, 'already_existed_shipments' => $present_shipments];
                }
                

            }
        
        }
        if(count($present_shipments) > 0){
            return redirect()->back()->with('success', 'Request(s) successfully added. Request againts these shipment already exits '.implode(',', $present_shipments));
        }else{
            return redirect()->back()->with('success', 'Request(s) successfully added');
        }
    }
}
