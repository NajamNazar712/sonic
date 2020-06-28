<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\CRM\CRMCommentController;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\CRM\CrmComments;
use App\Http\Models\CRM\CrmPaymentShipment;
use App\Http\Models\CRM\CrmRequest;
use App\Http\Models\CRM\CrmRequestAgentHistory;
use App\Http\Models\CRM\CrmRequestStatusHistory;
use App\Http\Models\CRM\CrmRequestTagging;
use App\Http\Models\CRM\DelayInDeliveryShipment;
use App\http\Models\CRM\Escalation\CrmEscalation;
use App\http\Models\CRM\Escalation\CrmEscalationShipmentStatus;
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
                                    $crm_comment = CrmComments::where('crm_request_id', $crm_request->id)->where('comment', $comment);
                                    if(!$crm_comment->exists()){
                                        $comment_by = 0;
                                        $comment_type = 0;
                                        CRMCommentController::add($crm_request->id, $agent_id,$comment_by,$comment_type, $comment);
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
}
