<?php

namespace App\Http\Controllers\Agent;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\RvShipmentAssignAgent;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Shipper\User;
use App\Http\Traits\RvTrait;
use App\Jobs\BotCallDispatch;
use App\RvAgentCallHistory;
use App\RvAssignAgentSubStatus;
use App\RvShipmentAgent;
use App\RvShipmentTicket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BotCallingController extends Controller
{
    use RvTrait;

    public function bot_get_ticket_details(Request $request)
    {
        // $this->rvshipmentticketInsert(378130,12,1,1049,0);
        // $shipmentId= 378130;
        // $data  = [
        //     'shipper_status_id' => 12,
        //     'shipment_status_reason_id' => 1,
        //     'shipment_user_id' => 1049,
        //     'call_count' => 0
        // ];
        //  (new BotCallDispatch($shipmentId))->delay(60 * 1);

        // $this->dispatch($job);    
            // dispatch((new BotCallDispatch($shipmentId))->delay(60 * 5));
        return 1;
        $trackingNo = $request->tracking_number;

        if(!$trackingNo)
        {
            return response()->json(['status' => 0, 'error' => 'Tracking number is required']);
        }

        $shipment = Shipment::select(
            'id',
            'tracking_number',
            'user_id',
            'amount',
            'consignee_name',
            'consignee_phone_number_1'
        )->where('tracking_number', $trackingNo)->first();
        if (!$shipment) {
            return response()->json(['status' => 0, 'error' => 'Invalid Tracking Number']);
        }

        $user = User::select('name')->find($shipment->user_id);
        $reason = RvShipmentTicket::select('shipment_status_reason_id')
        ->where('shipment_id',$shipment->id)->first();
        if($reason && isset($reason->shipment_status_reason_id))
        {
            $reason = RvAssignAgentSubStatus::select('name')->where('id', $reason->shipment_status_reason_id)->first();
            $reason = $reason->name;
        }

        $data = [
            'tracking_number' => $shipment->tracking_number,
            'shipper_name' => $user->name,
            'cod_amount' => $shipment->amount,
            'consignee_name' => $shipment->consignee_name,
            'consignee_number' => $shipment->consignee_phone_number_1,
            'undelivered_reason' => $reason ?? "No Reason Found!",
        ];

        return response()->json(['status' => 1,'data' => $data]);
    }

    public function bot_submit_ticket(Request $request)
    {
        $validations = [
            'tracking_number' => 'required|exists:shipments,tracking_number',
            'call_status' => 'required',
            'input' => 'required'
        ];

        $data = [
            'tracking_number' => $request->input('tracking_number'),
            'call_status' => $request->input('call_status'),
            'input' => $request->input('input')
        ];
        $validate = Validator::make($data, $validations);
        if ($validate->fails()) {
            return response()->json(['status' => 0, 'errors' => $validate->errors()],422);
        } 
        $findShipmentId = Shipment::where('tracking_number', $request->input('tracking_number'))->whereIn('shipper_status_id', [12, 52, 66])->first();    
        
        if($findShipmentId && RvShipmentTicket::where('shipment_id', $findShipmentId->id)->whereNull('deleted_at')->where('is_bot', 1)->exists()){
            // RvShipmentTicket::where('shipment_id', $findShipmentId->id)->update(['in_progress' => 1]);
            $array = [
                0 => [
                    'status_id' => 6,
                    'remarks' => 'unresponsive'
                ], // unresponsive
                1 => [
                    'status_id' => 2,
                    'remarks' => 'reattempt'
                ], // reattempt
                2 => [
                    'status_id' => 1,
                    'remarks' => 'retrurn'
                ], // retrurn
                3 => [
                    'status_id' => 3,
                    'remarks' => 'assign to the manual agent'
                ], // retrurn
            ];
            $shipment_assign_agent = RvShipmentAssignAgent::where('shipment_id', $findShipmentId->id)->where('rv_state_id', 1)->latest()->first();
            $request->request->add(['agent_id' => $request->admin_id, 'shipment_id' => $findShipmentId->id, 'rv_assign_agent_status_id' => $request->call_status, 'remarks' => 'bot call add the ' . $array[$request->input]['remarks'], 'rv_assign_agent_status_id' => $array[$request->input]['status_id'], 'call_count' => 1]);
            Log::channel('cronJobLog')->info('s ' . 'bot-call- parameters'. json_encode($request->all()));

            // if($shipment_assign_agent){
            if ($request->input > 0) {
                $status = new RvAgentCallHistory();
                $status->shipment_id = $request->shipment_id;
                $status->rv_shipment_assign_agent_id = $shipment_assign_agent->id;
                $status->call_finding_id = 1; //call finding reasons
                $status->call_to_id = 1; //Shipper or Consignee
                $status->remarks = $request->remarks;
                $status->updated_type_id = Auth::guard('agent')->check() ? 2 : 1;
                $status->updated_by_id = $request->admin_id;
                $status->save();
            }

            $assigned_agent = RvShipmentAgent::where('agent_id', $request->admin_id)->first();
            $admin_agent = Admin::where('id', $request->admin_id)->first();
            $this->update_shipment_assign_agent($request, $assigned_agent, $admin_agent, $shipment_assign_agent);
            $shipments_journey = ShipmentsJourney::where('shipment_id', $request->shipment_id)->latest()->first();
            if ($request->input != 3) { // not for the further assistance
                $data = $this->update_shipment_status($request, 1);
                if ($data['status'] == 1) { //data add successfully
                    $shipment_assign_agent = RvShipmentAssignAgent::where('shipment_id', $findShipmentId->id)->latest()->first();
                    //Dispatch Job Add with the delay for the unresponsive case second or third call
                    if ($request->input < 1) {
                        unset($data['message']['rv_agent_call_history_record_id']);

                        if (RvShipmentAssignAgent::join('rv_shipment_tickets as rst', 'rst.shipment_id', 'rv_shipment_assign_agents.shipment_id')->where('rv_shipment_assign_agents.unresponsive_count', 1)->where('rv_shipment_assign_agents.shipment_id', $findShipmentId->id)->exists()) {

                            $globalSettingValue = GlobalSettings::where('type', 'second_bot_call')->select('setting_value')->first();
                            $job = (new BotCallDispatch($findShipmentId->id))->delay(60 * $globalSettingValue['setting_value']);
                            $this->dispatch($job);
                        } elseif (RvShipmentAssignAgent::join('rv_shipment_tickets as rst', 'rst.shipment_id', 'rv_shipment_assign_agents.shipment_id')->where('rv_shipment_assign_agents.unresponsive_count', 2)->where('rv_shipment_assign_agents.shipment_id', $findShipmentId->id)->exists()) {

                            $globalSettingValue = GlobalSettings::where('type', 'third_bot_call')->select('setting_value')->first();
                            $job = (new BotCallDispatch($findShipmentId->id))->delay(60 * $globalSettingValue['setting_value']);
                            $this->dispatch($job);
                        }
                    }
                    $this->rv_shipment_assign_agent_details($request, $shipment_assign_agent, $shipments_journey,  $status->id ?? $data['rv_agent_call_history_record_id']);

                }
            } else {
                $this->rv_shipment_assign_agent_details($request, $shipment_assign_agent, $shipments_journey, $status->id);

                RvShipmentTicket::where('shipment_id', $findShipmentId->id)->update(['in_progress' => 0, 'is_bot' => 0]);
                $data = [
                    'status' => 1,
                    'message' => 'Shipment is assign to manual agent'
                ];
            }
        }else{
            $data = [
                'status' => 0,
                'message' => 'Shipment is in different status, Cannot mark it as Another Status!'
            ];
        }
        Log::channel('cronJobLog')->info('s ' . ' bot-call- message-status update' . json_encode($data));
        
        return response()->json(['status' => 1,'message' => $data]);
    }
}
