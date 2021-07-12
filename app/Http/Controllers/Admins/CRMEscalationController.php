<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\CRM\CRMCommentController;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\AdminHub;
use App\Http\Models\Admin\AdminRole;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\CRM\CrmComments;
use App\Http\Models\CRM\CrmPaymentShipment;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestAgentHistory;
use App\http\Models\CRM\CrmRequestEscalationTagging;
use App\Http\Models\CRM\CrmRequestStatusHistory;
use App\Http\Models\CRM\CrmRequestTagging;
use App\Http\Models\CRM\DelayInDeliveryShipment;
use App\http\Models\CRM\Escalation\CrmEscalation;
use App\http\Models\CRM\Escalation\CrmEscalationShipmentStatus;
use App\http\Models\CRM\Escalation\CrmEscalationTagging;
use App\http\Models\CRM\Escalation\CrmEscalationTaggingHub;
use App\http\Models\CRM\Escalation\CrmEscalationTaggingShipmentStatus;
use App\Http\Models\CRM\Escalation\CrmRequestEscalationLog;
use App\Http\Models\CRM\Escalation\CrmRequestEscalationStatus;
use App\Http\Models\ShipmentsJourney;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CRMEscalationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');

        $this->middleware('Permission');
    }

    static public function launched_in_process_requests(){
        $crm_requests = CrmRequest::whereIn('status_id', [1,2,5])->where('case_nature_id', '!=', 3)->get();
        $setting = GlobalSettings::where('type', 'crm_default_agent');
        if($setting->exists()){
            $setting = $setting->first();
            $agent_id = $setting->setting_value;
        }
        else{
            $agent_id = 306;
        }
        foreach ($crm_requests as $crm_request){
            $case_nature = $crm_request->case_nature_id;
            $case_nature_type = $crm_request->case_nature_type_id;
            $shipper_status_id = $crm_request->shipment->shipper_status_id;
            if($shipper_status_id != 1){
                if($crm_request->status_id == 1 || $crm_request->status_id == 5){
                    $crm_escalations = CrmEscalation::where('case_nature', $case_nature)->where('case_nature_type', $case_nature_type)->where('crm_request_status', 1)->where('status', 1);
                }
                else{
                    $crm_escalations = CrmEscalation::where('case_nature', $case_nature)->where('case_nature_type', $case_nature_type)->where('crm_request_status', 2)->where('status', 1);
                }
                if($crm_escalations->exists()){
                    $crm_escalations = $crm_escalations->get();
                    foreach ($crm_escalations as $crm_escalation){
                        $crm_escalation_shipment_status = CrmEscalationShipmentStatus::where('escalation_id', $crm_escalation->id)->where('shipment_status_id', $shipper_status_id);
                        if($crm_escalation_shipment_status->exists()){
                            $shipment_id = $crm_request->shipment_id;
                            $arrival_of_shipment = ShipmentsJourney::where('shipment_id', $shipment_id)->where('shipper_status_id', 2);
                            if($arrival_of_shipment->exists()){
                                $arrival_of_shipment = $arrival_of_shipment->first();

                                $tat_date = Carbon::parse($arrival_of_shipment->created_at)->addDays($crm_escalation->tat);
                                $current_date = Carbon::now();
                                if($current_date > $tat_date){
                                    if($crm_request->agent_id == null){
                                        $crm_request->agent_id = $agent_id;

                                        $crm_agent_history = new CrmRequestAgentHistory();
                                        $crm_agent_history->crm_request_id = $crm_request->id;
                                        $crm_agent_history->agent_id = $agent_id;
                                        $crm_agent_history->assigned_by = $agent_id;
                                        $crm_agent_history->save();
                                    }
                                    $prev_status = $crm_request->status_id;
                                    if($crm_request->status_id == 1 || $crm_request->status_id == 5){
                                        if($crm_escalation->mark_as == 1){
                                            $status_id = 2;
                                            $history_status_id = 6;
                                            $crm_status_history = new CrmRequestStatusHistory();
                                            $crm_status_history->crm_request_id = $crm_request->id;
                                            $crm_status_history->status_id = $history_status_id;
                                            $crm_status_history->agent_id = $agent_id;
                                            $crm_status_history->save();

                                            if($crm_request->case_nature_type_id == 2 && $crm_request->shipment_id != null){
                                                AdminCRMController::delay_in_delivery_shipment_add($crm_request->id, $crm_request->shipment_id);
                                            }
                                            if($prev_status == 1){
                                                if($crm_request->case_nature_type_id == 1 && $crm_request->shipment_id != null){
                                                    AdminCRMController::automation_payment_add($crm_request->id, $crm_request->shipment_id);
                                                }
                                            }
                                        }
                                        else{
                                            $status_id = 4;
                                            $history_status_id = 7;
                                            $crm_status_history = new CrmRequestStatusHistory();
                                            $crm_status_history->crm_request_id = $crm_request->id;
                                            $crm_status_history->status_id = $history_status_id;
                                            $crm_status_history->agent_id = $agent_id;
                                            $crm_status_history->save();

                                            CrmRequestTagging::where('crm_request_id', $crm_request->id)->delete();
                                        }

                                        $crm_request->status_id = $status_id;

                                        $crm_status_history = new CrmRequestStatusHistory();
                                        $crm_status_history->crm_request_id = $crm_request->id;
                                        $crm_status_history->status_id = $status_id;
                                        $crm_status_history->agent_id = $agent_id;
                                        $crm_status_history->save();

                                        $crm_request->save();
                                    }
                                    elseif($crm_request->status_id == 2){
                                        if($crm_escalation->mark_as == 1){
                                            $status_id = 3;

                                            $crm_request->status_id = $status_id;

                                            $crm_status_history = new CrmRequestStatusHistory();
                                            $crm_status_history->crm_request_id = $crm_request->id;
                                            $crm_status_history->status_id = $status_id;
                                            $crm_status_history->agent_id = $agent_id;
                                            $crm_status_history->save();

                                            $crm_request->save();

                                            if(DelayInDeliveryShipment::where('crm_request_id', $crm_request->id)->exists()){
                                                DelayInDeliveryShipment::where('crm_request_id', $crm_request->id)->delete();
                                            }
                                            if(CrmPaymentShipment::where('crm_request_id', $crm_request->id)->exists()){
                                                CrmPaymentShipment::where('crm_request_id', $crm_request->id)->delete();
                                            }
                                        }
                                    }

                                    $comment = $crm_escalation->comment;
                                    if($comment != null){
                                        $crm_comment = CrmComments::where('crm_request_id', $crm_request->id)->where('comment', $comment);
                                        if(!$crm_comment->exists()){
                                            $comment_by = 0;
                                            $comment_type = 0;
                                            CRMCommentController::add($crm_request->id, $agent_id,$comment_by,$comment_type, $comment,1);
                                        }
                                    }
                                }
                                break;
                            }
                        }
                    }
                }
            }
        }
    }

    static public function in_process_requests_tagging(){
        $setting = GlobalSettings::where('type', 'crm_default_agent');
        $crm_requests = CrmRequest::where('status_id', 2)->where('case_nature_id', '!=', 3);
        $processed_crm_requests = array();
        if($crm_requests->exists()){
            $crm_requests = $crm_requests->get();
            foreach ($crm_requests as $crm_request){
                $flag = true;
                $crm_request_escalation_status = CrmRequestEscalationStatus::where('crm_request_id', $crm_request->id);
                if($crm_request_escalation_status->exists()){
                    $crm_request_escalation_status = $crm_request_escalation_status->first();
                    if($crm_request_escalation_status->status == 0){
                        $flag = false;
                    }
                }
                if($flag == true){
                    $case_nature = $crm_request->case_nature_id;
                    $case_nature_type = $crm_request->case_nature_type_id;
                    $shipper_status_id = $crm_request->shipment->shipper_status_id;
                    $crm_escalation_tagging = CrmEscalationTagging::where('case_nature', $case_nature)->where('case_nature_type', $case_nature_type)->whereNotNull('hub_status')->where('status', 1);

                    if($crm_escalation_tagging->exists()) {
                        $crm_escalation_tagging = $crm_escalation_tagging->get();
                        foreach ($crm_escalation_tagging as $crm_escalation_tag){
                            $crm_escalation_shipment_status = CrmEscalationTaggingShipmentStatus::where('escalation_tagging_id', $crm_escalation_tag->id)->where('shipment_status_id', $shipper_status_id);
                            $matching_hubs = array();
                            if($crm_escalation_tag->hub_status == 1){
                                $origin_hub_id = $crm_request->shipment->pickup_address->city->hub_id;
                                $matching_hubs[] = $origin_hub_id;
                            }
                            elseif($crm_escalation_tag->hub_status == 2){
                                $destination_hub_id = $crm_request->shipment->consignee_city->hub_id;
                                $matching_hubs[] = $destination_hub_id;

                            }
                            elseif($crm_escalation_tag->hub_status == 3){
                                $origin_hub_id = $crm_request->shipment->pickup_address->city->hub_id;
                                $matching_hubs[] = $origin_hub_id;
                                $destination_hub_id = $crm_request->shipment->consignee_city->hub_id;
                                if($origin_hub_id != $destination_hub_id){
                                    $matching_hubs[] = $destination_hub_id;
                                }
                            }

                            $crm_escalation_hubs = CrmEscalationTaggingHub::where('escalation_tagging_id', $crm_escalation_tag->id)->whereIn('hub_id', $matching_hubs);
                            if($crm_escalation_shipment_status->exists() && $crm_escalation_hubs->exists()){
                                $hubs = $crm_escalation_tag->hubs;
                                $crm_request_in_process = CrmRequestStatusHistory::where('crm_request_id', $crm_request->id)->where('status_id', 2)->latest()->first();
                                $current_date = Carbon::today();
                                foreach ($crm_escalation_tag->levels as $level){
                                    $log = CrmRequestEscalationLog::where('crm_request_id', $crm_request->id)->where('escalation_tagging_id', $crm_escalation_tag->id)->where('level_id', $level->level_id);
                                    if(!$log->exists()){
                                        $crm_request_in_process_date_after_tat = Carbon::parse($crm_request_in_process->created_at)->addDays($level->tat);
                                        if($current_date > $crm_request_in_process_date_after_tat){
                                            $tagging_to = array();
                                            $to = array();
                                            $cc = array();
                                            $bcc = array();
                                            $escalation_emails = array();
                                            $escalation_emails['level'] = $level->level->name .'(' . $level->level->id . ')';
                                            $roles = $level->roles;
                                            foreach ($roles as $role)
                                            {
                                                $admins = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')
                                                    ->where('admin_roles.id', $role->role_id)
                                                    ->where('a.status', 1)
                                                    ->select('a.id as id', 'a.email as email')
                                                    ->get();
                                                if(count($hubs) > 0){
                                                    $total_hubs = array();
                                                    foreach ($hubs as $hub){
                                                        if(in_array($hub->hub_id, $matching_hubs)){
                                                            $crm_request_multiple_tagging = new CrmRequestEscalationTagging();
                                                            $crm_request_multiple_tagging->crm_request_id = $crm_request->id;
                                                            $crm_request_multiple_tagging->role_id = $role->role_id;
                                                            $crm_request_multiple_tagging->hub_id = $hub->hub_id;
                                                            $crm_request_multiple_tagging->save();

                                                            $total_hubs[] = $hub->hub_id;
                                                        }
                                                    }

                                                    foreach ($admins as $admin){
                                                        $admin_hubs = AdminHub::where('admin_id', $admin->id)->whereIn('hub_id', $total_hubs);
                                                        if($admin_hubs->exists()){
                                                            $tagging_to[] = $admin->email;
                                                        }
                                                    }
                                                }
                                                else{
                                                    $crm_request_multiple_tagging = new CrmRequestEscalationTagging();
                                                    $crm_request_multiple_tagging->crm_request_id = $crm_request->id;
                                                    $crm_request_multiple_tagging->role_id = $role->role_id;
                                                    $crm_request_multiple_tagging->hub_id = NULL;
                                                    $crm_request_multiple_tagging->save();


                                                    foreach ($admins as $admin){
                                                        $tagging_to[] = $admin->email;
                                                    }
                                                }
                                            }
                                            $emails = $level->emails;
                                            foreach ($emails as $email){
                                                if($email->status == 1){
                                                    $to[] = $email->email;
                                                }
                                                else if($email->status == 2){
                                                    $cc[] = $email->email;
                                                }
                                                else if($email->status == 3){
                                                    $bcc[] = $email->email;
                                                }
                                            }
                                            $escalation_emails['to'] = $to;
                                            $escalation_emails['cc'] = $cc;
                                            $escalation_emails['bcc'] = $bcc;

                                            $new_log = new CrmRequestEscalationLog();
                                            $new_log->crm_request_id = $crm_request->id;
                                            $new_log->escalation_tagging_id = $crm_escalation_tag->id;
                                            $new_log->tagging_level_id = $level->id;
                                            $new_log->level_id = $level->level_id;
                                            $new_log->save();
                                            NotificationsController::send(65, $crm_request->id, $tagging_to);
                                            NotificationsController::send(66, $crm_request->id, $escalation_emails);
                                        }
                                        else{
                                            break;
                                        }
                                    }
                                }
                                $processed_crm_requests[] = $crm_request->id;
                                break;
                            }
                        }
                    }
                    else{
                        $crm_escalation_tagging = CrmEscalationTagging::where('case_nature', $case_nature)->where('case_nature_type', $case_nature_type)->whereNull('hub_status')->where('status', 1);

                        if($crm_escalation_tagging->exists()) {
                            $crm_escalation_tagging = $crm_escalation_tagging->get();
                            foreach ($crm_escalation_tagging as $crm_escalation_tag){
                                $crm_escalation_shipment_status = CrmEscalationTaggingShipmentStatus::where('escalation_tagging_id', $crm_escalation_tag->id)->where('shipment_status_id', $shipper_status_id);
                                if($crm_escalation_shipment_status->exists()){
                                    $crm_request_in_process = CrmRequestStatusHistory::where('crm_request_id', $crm_request->id)->where('status_id', 2)->latest()->first();
                                    $current_date = Carbon::today();
                                    foreach ($crm_escalation_tag->levels as $level){
                                        $log = CrmRequestEscalationLog::where('crm_request_id', $crm_request->id)->where('escalation_tagging_id', $crm_escalation_tag->id)->where('level_id', $level->level_id);
                                        if(!$log->exists()){
                                            $crm_request_in_process_date_after_tat = Carbon::parse($crm_request_in_process->created_at)->addDays($level->tat);
                                            if($current_date > $crm_request_in_process_date_after_tat){
                                                $tagging_to = array();
                                                $to = array();
                                                $cc = array();
                                                $bcc = array();
                                                $escalation_emails = array();
                                                $escalation_emails['level'] = $level->level->name .'(' . $level->level->id . ')';
                                                $roles = $level->roles;
                                                foreach ($roles as $role)
                                                {
                                                    $admins = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')
                                                        ->where('admin_roles.id', $role->role_id)
                                                        ->where('a.status', 1)
                                                        ->select('a.id as id', 'a.email as email')
                                                        ->get();

                                                    $crm_request_multiple_tagging = new CrmRequestEscalationTagging();
                                                    $crm_request_multiple_tagging->crm_request_id = $crm_request->id;
                                                    $crm_request_multiple_tagging->role_id = $role->role_id;
                                                    $crm_request_multiple_tagging->hub_id = NULL;
                                                    $crm_request_multiple_tagging->save();


                                                    foreach ($admins as $admin){
                                                        $tagging_to[] = $admin->email;
                                                    }
                                                }
                                                $emails = $level->emails;
                                                foreach ($emails as $email){
                                                    if($email->status == 1){
                                                        $to[] = $email->email;
                                                    }
                                                    else if($email->status == 2){
                                                        $cc[] = $email->email;
                                                    }
                                                    else if($email->status == 3){
                                                        $bcc[] = $email->email;
                                                    }
                                                }
                                                $escalation_emails['to'] = $to;
                                                $escalation_emails['cc'] = $cc;
                                                $escalation_emails['bcc'] = $bcc;

                                                $new_log = new CrmRequestEscalationLog();
                                                $new_log->crm_request_id = $crm_request->id;
                                                $new_log->escalation_tagging_id = $crm_escalation_tag->id;
                                                $new_log->tagging_level_id = $level->id;
                                                $new_log->level_id = $level->level_id;
                                                $new_log->save();
                                                NotificationsController::send(65, $crm_request->id, $tagging_to);
                                                NotificationsController::send(66, $crm_request->id, $escalation_emails);
                                            }
                                            else{
                                                break;
                                            }
                                        }
                                    }
                                    $processed_crm_requests[] = $crm_request->id;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
        $crm_requests = CrmRequest::where('status_id', 2)->where('case_nature_id', '!=', 3)->whereNotIn('id', $processed_crm_requests);
        if($crm_requests->exists()){
            $crm_requests = $crm_requests->get();
            foreach ($crm_requests as $crm_request){
                $crm_escalation_tagging = CrmEscalationTagging::where('case_nature', $case_nature)->where('case_nature_type', $case_nature_type)->whereNull('hub_status')->where('status', 1);

                if($crm_escalation_tagging->exists()) {
                    $crm_escalation_tagging = $crm_escalation_tagging->get();
                    foreach ($crm_escalation_tagging as $crm_escalation_tag){
                        $crm_escalation_shipment_status = CrmEscalationTaggingShipmentStatus::where('escalation_tagging_id', $crm_escalation_tag->id)->where('shipment_status_id', $shipper_status_id);
                        if($crm_escalation_shipment_status->exists()){
                            $crm_request_in_process = CrmRequestStatusHistory::where('crm_request_id', $crm_request->id)->where('status_id', 2)->latest()->first();
                            $current_date = Carbon::today();
                            foreach ($crm_escalation_tag->levels as $level){
                                $log = CrmRequestEscalationLog::where('crm_request_id', $crm_request->id)->where('escalation_tagging_id', $crm_escalation_tag->id)->where('level_id', $level->level_id);
                                if(!$log->exists()){
                                    $crm_request_in_process_date_after_tat = Carbon::parse($crm_request_in_process->created_at)->addDays($level->tat);
                                    if($current_date > $crm_request_in_process_date_after_tat){
                                        $tagging_to = array();
                                        $to = array();
                                        $cc = array();
                                        $bcc = array();
                                        $escalation_emails = array();
                                        $escalation_emails['level'] = $level->level->name .'(' . $level->level->id . ')';
                                        $roles = $level->roles;
                                        foreach ($roles as $role)
                                        {
                                            $admins = AdminRole::leftjoin('admins as a', 'a.role_id', '=', 'admin_roles.id')
                                                ->where('admin_roles.id', $role->role_id)
                                                ->where('a.status', 1)
                                                ->select('a.id as id', 'a.email as email')
                                                ->get();

                                            $crm_request_multiple_tagging = new CrmRequestEscalationTagging();
                                            $crm_request_multiple_tagging->crm_request_id = $crm_request->id;
                                            $crm_request_multiple_tagging->role_id = $role->role_id;
                                            $crm_request_multiple_tagging->hub_id = NULL;
                                            $crm_request_multiple_tagging->save();


                                            foreach ($admins as $admin){
                                                $tagging_to[] = $admin->email;
                                            }
                                        }
                                        $emails = $level->emails;
                                        foreach ($emails as $email){
                                            if($email->status == 1){
                                                $to[] = $email->email;
                                            }
                                            else if($email->status == 2){
                                                $cc[] = $email->email;
                                            }
                                            else if($email->status == 3){
                                                $bcc[] = $email->email;
                                            }
                                        }
                                        $escalation_emails['to'] = $to;
                                        $escalation_emails['cc'] = $cc;
                                        $escalation_emails['bcc'] = $bcc;

                                        $new_log = new CrmRequestEscalationLog();
                                        $new_log->crm_request_id = $crm_request->id;
                                        $new_log->escalation_tagging_id = $crm_escalation_tag->id;
                                        $new_log->tagging_level_id = $level->id;
                                        $new_log->level_id = $level->level_id;
                                        $new_log->save();
                                        NotificationsController::send(65, $crm_request->id, $tagging_to);
                                        NotificationsController::send(66, $crm_request->id, $escalation_emails);
                                    }
                                    else{
                                        break;
                                    }
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }
    }
}
