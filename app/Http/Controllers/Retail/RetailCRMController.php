<?php

namespace App\Http\Controllers\Retail;

use App\Http\Controllers\CRM\CRMController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\AdminDepartment;
use App\Http\Models\Admin\AdminRole;
use App\Http\Models\Admin\Retail\RetailUser;
use App\Http\Models\Admin\SalePersonTag;
use App\Http\Models\City;
use App\Http\Models\CRM\CrmComments;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestAgentHistory;
use App\Http\Models\CRM\CrmRequestCaseNature;
use App\Http\Models\CRM\CrmRequestCaseNatureType;
use App\Http\Models\CRM\CrmRequestEscalationTagging;
use App\Http\Models\CRM\CrmRequestStatusHistory;
use App\Http\Models\CRM\CrmRequestTagging;
use App\Http\Models\CRM\CrmRequestTaggingHistory;
use App\Http\Models\CRM\CrmRequestTaggingTypes;
use App\Http\Models\CRM\Escalation\CrmEscalationLevel;
use App\Http\Models\CRM\Escalation\CrmEscalationTaggingLevel;
use App\Http\Models\CRM\Escalation\CrmRequestEscalationLog;
use App\Http\Models\CRM\Escalation\CrmRequestEscalationStatus;
use App\Http\Models\DonePayment;
use App\Http\Models\DonePaymentShipment;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Shipper\SubstituteUser;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RetailCRMController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:retail');

//        $this->middleware('Permission');
    }

    public function add_request(Request $request){

        $nature_id = $request->case_nature_id;
        $complaint_id = $request->complaint_id;
        $channel_id = $request->channel_id;
        $description = $request->description;
        $flag = false;
        $cannot_change = false;
        $present_shipments = array();
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
                        $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 3, $shipment->id, $shipment->user_id, NULL ,$description);
                        if($request->has('key_account')){
                            $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
                        }
                    }
                    $crm_request_padded_id = str_pad($crm_request_padded_id, 6, 0, STR_PAD_LEFT);

                    return ['status' => 1, 'success' => 'Request ('. $crm_request_padded_id .') successfully added'];
                }else{
                    return ['status' => 0, 'error' => 'No Payment selected!'];
                }
            }
        }
        else{
            if ($request->has('shipment_ids')) {
                if ($nature_id == 4) {
                    $shipment_ids = explode(',', $request->input('shipment_ids'));
                }
                else{
                    $shipment_ids = $request->shipment_ids;
                }
                if(!empty($shipment_ids)){
                    foreach ($shipment_ids as $shipment_id) {
                        $shipment = Shipment::find($shipment_id);
                        if($shipment){

                            $is_shipment = CrmRequest::where('shipment_id',$shipment_id)->where('case_nature_id',$nature_id)->first();
                            if($is_shipment){
                                $complain = $is_shipment->id;
                                if($is_shipment->case_nature_id != $nature_id){
                                    if ($nature_id == 4) {
                                        if($complaint_id == 26){
                                            $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 3, $shipment_id, $shipment->user_id, NULL , $description);
                                        }
                                        else{
                                            $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 3, $shipment_id, $shipment->user_id, NULL , $description, $request->product_cost,  $request->file('product_picture'), $request->file('invoice_picture'));
                                        }
                                        if($request->has('key_account')){
                                            $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
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
                                                $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 3, $shipment_id, $shipment->user_id, NULL ,$description);
                                                if($request->has('key_account')){
                                                    $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
                                                }
                                            }
                                        }
                                        else{
                                            $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 3, $shipment_id, $shipment->user_id, NULL ,$description);
                                            if($request->has('key_account')){
                                                $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
                                            }
                                        }
                                    }
                                }else{
                                    $present_shipments[] = $shipment->tracking_number;
                                    $present_shipments[] = 'Complaint ID: '. $complain;
                                    $flag = true;
                                }
                            }
                            else{
                                if ($nature_id == 4) {
                                    $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 3, $shipment_id, $shipment->user_id, NULL , $description, $request->product_cost,  $request->file('product_picture'), $request->file('invoice_picture'));
                                    if($request->has('key_account')){
                                        $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
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
                                            $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 3, $shipment_id, $shipment->user_id, NULL ,$description);
                                            if($request->has('key_account')){
                                                $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
                                            }
                                        }
                                    }
                                    else{
                                        $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 3, $shipment_id, $shipment->user_id, NULL ,$description);
                                        if($request->has('key_account')){
                                            $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    return ['status' => 1, 'success' => 'Request(s) successfully added', 'flag' => $flag, 'already_existed_shipments' => $present_shipments, 'cannot_change' => $cannot_change];
                }else{
                    return ['status' => 0, 'error' => 'No shipments selected!'];
                }
            }
            else{
                $shipment_id = $request->shipment_id;
                if(!empty($shipment_id)){
                    $shipment = Shipment::find($shipment_id);
                    if($shipment){
                        $is_shipment = CrmRequest::where('shipment_id',$shipment_id)->first();
                        $is_shipment = CrmRequest::where('shipment_id',$shipment_id)->where('case_nature_id',$nature_id)->first();
                        if($is_shipment){
                            $complain = $is_shipment->id;
                            if($is_shipment->case_nature_id != $nature_id){
                                if($shipment->shipper_status_id == 20 || $shipment->shipper_status_id == 1){
                                    if(in_array($complaint_id, [11, 13])){
                                        $present_shipments[] = $shipment->tracking_number;
                                        $flag = true;
                                        $cannot_change = true;
                                    }
                                    else{
                                        $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 3, $shipment_id, $shipment->user_id, NULL ,$description);
                                        if($request->has('key_account')){
                                            $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
                                        }
                                        $crm_request_padded_id = str_pad($crm_request_padded_id, 6, 0, STR_PAD_LEFT);
                                        return ['status' => 1, 'success' => 'Request ('. $crm_request_padded_id .') successfully added', 'flag' => $flag, 'already_existed_shipments' => $present_shipments];
                                    }
                                }
                                else{
                                    $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 3, $shipment_id, $shipment->user_id, NULL ,$description);
                                    if($request->has('key_account')){
                                        $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
                                    }
                                    $crm_request_padded_id = str_pad($crm_request_padded_id, 6, 0, STR_PAD_LEFT);
                                    return ['status' => 1, 'success' => 'Request ('. $crm_request_padded_id .') successfully added', 'flag' => $flag, 'already_existed_shipments' => $present_shipments];
                                }
                            }else{
                                $present_shipments[] = $shipment->tracking_number;
                                $present_shipments[] = 'Complaint ID: '. $complain;
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
                                    $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 3, $shipment_id, $shipment->user_id, NULL ,$description);
                                    if($request->has('key_account')){
                                        $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
                                    }
                                    $crm_request_padded_id = str_pad($crm_request_padded_id, 6, 0, STR_PAD_LEFT);
                                    return ['status' => 1, 'success' => 'Request ('. $crm_request_padded_id .') successfully added', 'flag' => $flag, 'already_existed_shipments' => $present_shipments];
                                }
                            }
                            else{
                                $crm_request_padded_id = CRMController::add($nature_id, $complaint_id, $channel_id, 1, Auth::id(), 3, $shipment_id, $shipment->user_id, NULL ,$description);
                                if($request->has('key_account')){
                                    $this->key_account_crm_summary_shipments($shipment->id, $crm_request_padded_id, Auth::id(), $channel_id, $complaint_id);
                                }
                                $crm_request_padded_id = str_pad($crm_request_padded_id, 6, 0, STR_PAD_LEFT);
                                return ['status' => 1, 'success' => 'Request ('. $crm_request_padded_id .') successfully added', 'flag' => $flag, 'already_existed_shipments' => $present_shipments];
                            }
                        }
                    }
                    return ['status' => 1, 'success' => 'Request(s) successfully added', 'flag' => $flag, 'already_existed_shipments' => $present_shipments, 'cannot_change' => $cannot_change];
                }else{
                    return ['status' => 0, 'error' => $request->shipment_id];
                }
            }
        }

    }

//    public function update_request(Request $request){
//        $request_id = $request->request_id;
//        $nature_id = $request->case_nature_id;
//        $complaint_id = $request->complaint_id;
//        $channel_id = $request->channel_id;
//        if($request_id != null){
//            $crm_request = CrmRequest::find($request_id);
//            $crm_request->case_nature_id = $nature_id;
//            $crm_request->case_nature_type_id = $complaint_id;
//            $crm_request->channel_id = $channel_id;
//            $crm_request->save();
//            return ['status' => 1, 'success' => 'Request successfully updated!'];
//        }
//        return ['status' => 0, 'error' => 'Request not found!'];
//
//    }
    public function add_feedback(Request $request){
        $nature_id = 3;
        $channel_id = $request->channel_id;
        $description = $request->description;
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
                        CRMController::add($nature_id, NULL, $channel_id, 1, Auth::id(), 3, $shipment_id, session('user_id'), NULL, $description);
                    } else {
                        $present_shipments[] = $shipment->tracking_number;
                        $flag = true;
                    }
                }else{
                    CRMController::add($nature_id, null, $channel_id, 1, Auth::id(), 3, $shipment_id, session('user_id'), NULL, $description);
                }
            }
        }
        else {
            if ($channel_id == null) {
                return ['status' => 0, 'error' => 'Channel Not selected!'];
            }
            if ($description == null) {
                return ['status' => 0, 'error' => 'Description Not Entered!'];
            }

            CRMController::add($nature_id, NULL, $channel_id, 1, Auth::id(), 3, NULL, NULL, NULL, $description);
        }
        return ['status' => 1, 'success' => 'Feedback successfully added', 'flag' => $flag, 'already_existed_shipments' => $present_shipments];

    }

    public function request_details(Request $request,$id){
        $crm_request = CrmRequest::find($id);
        if($crm_request){
            $shipment_status = null;
            $shipment_status_date = null;
            $shipper = null;
            $arrival_date = '';
            if($crm_request->shipment_id != null) {
                $shipment_status = Shipment::find($crm_request->shipment_id);
                $shipment_status = $shipment_status->status_shipper->name;
                $journey = ShipmentsJourney::where('shipment_id', $crm_request->shipment_id)->where('shipper_status_id', '=', 2);
                if($journey->exists()){
                    $journey = $journey->latest()->first();
                    $arrival_date = $journey->created_at;
                }
                $shipment_status_journey = ShipmentsJourney::where('shipment_id', $crm_request->shipment_id)->latest('id')->first();
                $shipment_status_date = $shipment_status_journey->created_at;
            }

            if($crm_request->shipper_id != null){
                $shipper = User::find($crm_request->shipper_id);
                $shipper = $shipper->name;
            }
            $admins = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id' )
                ->select('a.id as id', 'a.name as name')
                ->whereNotIn('admin_roles.department_id', [1,3])->get();
            $types = CrmRequestTaggingTypes::get();
            $departments = AdminDepartment::whereNotIn('id', [1,3])->get();
            $hubs = City::where('hub', 1)->get();
            $tagged = CrmRequestTagging::where('crm_request_id', $crm_request['id'])->first();
            $tagged_name = '';
            $tag_check = '';
            $tag_permission = '';
            if($tagged){
                if($tagged['crm_request_tagging_type_id'] == 1){
                    $tag_check = Auth::user()->role_id;
                    $tag = AdminRole::where('id', $tag_check)->first();
                    $tag_permission = $tag->department_id;
                    $tagged_name = AdminDepartment::find($tagged['tagged_id'])->name;
                }
                else if($tagged['crm_request_tagging_type_id'] == 2){
                    $tagged_name = Admin::find($tagged['tagged_id'])->name;
                }
            }

            $tagged_kae = CrmRequestTagging::where('crm_request_id', $crm_request['id'])->where('crm_request_tagging_type_id', 4)->get()->first();
            if($tagged_kae){
                $tagged_kae_name = Admin::find($tagged_kae->tagged_id)->name;
            }else{
                $tagged_kae_name = '';
            }
            $tagged_operation = CrmRequestTagging::where('crm_request_id', $crm_request['id'])->where('crm_request_tagging_type_id', 5)->get()->first();
            if($tagged_operation){
                $tagged_operation_name = Admin::find($tagged_operation->tagged_id);
                if($tagged_operation_name){
                    $tagged_operation_name = $tagged_operation_name->name;
                }else{
                    $tagged_operation_name = '';
                }
            }else{
                $tagged_operation_name = '';
            }

            $escalation_tagged = CrmRequestEscalationTagging::where('crm_request_id', $crm_request['id'])
                ->where('role_id', session('role_id'))
                ->where(function ($sub_sub_query) {
                    $sub_sub_query->whereNull('hub_id')
                        ->orWhereNotNull('hub_id')
                        ->whereIn('hub_id', session('hubs'));
                });
            if($escalation_tagged->exists()){
                $escalation_tagged_check = true;
            }
            else{
                $escalation_tagged_check = false;
            }
            $agent = Admin::where('id', $crm_request['agent_id'])->first();
            $agent_name = '';
            if($agent){
                $agent_name = $agent['name'];
            }

            $crm_comments = array();
            $last_comment = null;
            $crm_comments = CrmComments::where('crm_request_id', $id);
            if($crm_comments->exists()){
                $crm_comments = $crm_comments->orderBy('created_at','asc')->get();
                $last_comment = CrmComments::where('crm_request_id', $id)->latest()->first();
                $last_comment = $last_comment->id;
            }

            $launched_by  = '';
            if($crm_request->launched_by == 0){
                $launched_by = $launched_by = $crm_request->launched_by_admin->name."(Admin)";
            }else if($crm_request->launched_by == 1){
                $launched_by = User::find($crm_request->launched_by_id)->name;
            }else if($crm_request->launched_by == 2){
                $launched_by = SubstituteUser::find($crm_request->launched_by_id)->name;
            }else if($crm_request->launched_by == 3){
                $launched_by = RetailUser::find($crm_request->launched_by_id)->name;
            }

            $crm_tagging = array();
            $crm_tagging_details = CrmRequestTagging::where('crm_request_id', $id)->first();
            if($crm_tagging_details){
                $crm_tagging = $crm_tagging_details;
            }
            $crm_agent_history = CrmRequestAgentHistory::where('crm_request_id', $id)->get();
            $crm_request_ids = array();
            if($crm_request->shipment_id){
                $all_crm_request_ids_for_shipment = CrmRequest::where('shipment_id',$crm_request->shipment_id)->pluck('id')->toArray();
                $crm_request_ids = $all_crm_request_ids_for_shipment;
            }else{
                $crm_request_ids = [$id];
            }
            $crm_status_history = CrmRequestStatusHistory::whereIn('crm_request_id', $crm_request_ids)->get();
            $crm_tagging_history = CrmRequestTaggingHistory::where('crm_request_id', $id)->get();
            $case_nature = CrmRequestCaseNature::where('id', '!=', 3)->get();
            $case_nature_type_complaints = CrmRequestCaseNatureType::where('nature_id', '=', 1)->where('status_id',1)->get();
            $case_nature_type_service_requests = CrmRequestCaseNatureType::where('nature_id', '=', 2)->where('status_id',1)->get();
            $case_nature_type_claims = CrmRequestCaseNatureType::where('nature_id', '=', 4)->where('status_id',1)->get();

            $sale_person = SalePersonTag::where('user_id', $crm_request->shipper_id)->where('status', 0)->first();

            $crm_escalation_tagging_history = CrmRequestEscalationTagging::where('crm_request_id', $id)->get();

            $escalation_status_flag = true;
            $escalation_log_flag = false;
            $escalation_tagging_id = NULL;
            $crm_escalation_levels = NULL;
            $crm_request_escalation_status = CrmRequestEscalationStatus::where('crm_request_id', $crm_request->id);
            if($crm_request_escalation_status->exists()){
                $crm_request_escalation_status = $crm_request_escalation_status->first();
                if($crm_request_escalation_status->status == 0){
                    $escalation_status_flag = false;
                }
            }
            $crm_request_escalation_log = CrmRequestEscalationLog::where('crm_request_id', $crm_request->id);
            if($crm_request_escalation_log->exists()){
                $crm_request_escalation_log = $crm_request_escalation_log->latest()->first();
                $previous_levels = CrmRequestEscalationLog::where('crm_request_id', $crm_request->id)->where('escalation_tagging_id', $crm_request_escalation_log->escalation_tagging_id)->pluck('level_id')->toArray();
                $crm_request_escalation_tagging = CrmEscalationTaggingLevel::where('escalation_tagging_id', $crm_request_escalation_log->escalation_tagging_id)->whereNotIn('level_id', $previous_levels)->pluck('level_id')->toArray();
                $crm_escalation_levels = CrmEscalationLevel::whereIn('id', $crm_request_escalation_tagging)->get();
                if(count($crm_escalation_levels) > 0){
                    $escalation_log_flag = true;
                    $escalation_tagging_id = $crm_request_escalation_log->escalation_tagging_id;
                }
            }

            $crm_images_count = $crm_request->images->count();

            return view('admin.crm.request_details')->with(['tagged_kae_name' => $tagged_kae_name, 'tagged_operation_name' => $tagged_operation_name, 'crm_details' => $crm_request, 'launched_by' => $launched_by, 'comments' => $crm_comments, 'last_comment_id' => $last_comment, 'admins' => $admins, 'types' => $types, 'departments' => $departments, 'tagged_name' => $tagged_name,'crm_tagging' => $crm_tagging, 'crm_agent_history' => $crm_agent_history, 'crm_status_history' => $crm_status_history, 'crm_tagging_history' => $crm_tagging_history, 'agent' => $agent_name, 'tag_check' => $tagged, 'tag_permission' => $tag_permission, 'shipment_status' => $shipment_status, 'shipper' => $shipper,'case_nature' => $case_nature, 'case_nature_complaints' => $case_nature_type_complaints, 'case_nature_service_requests' => $case_nature_type_service_requests, 'arrival_date' => $arrival_date, 'shipment_status_date' => $shipment_status_date, 'sale_person' => $sale_person, 'case_nature_type_claims' => $case_nature_type_claims, 'hubs' => $hubs, 'escalation_tagged_check' => $escalation_tagged_check, 'crm_escalation_tagging_history' => $crm_escalation_tagging_history, 'escalation_status_flag' => $escalation_status_flag, 'escalation_log_flag' => $escalation_log_flag, 'escalation_tagging_id' => $escalation_tagging_id, 'crm_escalation_levels' => $crm_escalation_levels, 'crm_images_count' => $crm_images_count]);
        }else{
            return redirect()->back()->with('danger', 'CRM Request Not found!');
        }
    }

}
