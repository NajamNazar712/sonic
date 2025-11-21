<?php

namespace App\Http\Controllers\Agent;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\RvShipmentAssignAgent;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Shipper\User;
use App\Http\Models\Webhook\ApiZongLog;
use App\Http\Traits\RvTrait;
use App\Jobs\BotCallDispatch;
use App\Jobs\BotCallDispatchSecod;
use App\Jobs\BotCallDispatchThird;
use App\Jobs\ProcessRvShipmentTicket;
use App\RvAgentCallHistory;
use App\RvAssignAgentSubStatus;
use App\RvCronLog;
use App\RvShipmentAgent;
use App\RvShipmentTicket;
use Carbon\Carbon;
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
        // NotificationsController::send(4, [48025341]);
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
        dispatch(new BotCallDispatchThird(48086411));
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
        // $job = DB::table('jobs')->latest()->first();
        // $createdAt = $job->created_at;
        // $availableAt = $job->available_at;
        // $availableAt = Carbon::createFromTimestamp($job->available_at);
        // $formattedDate = $availableAt->format('Y-m-d H:i:s');

        // [dd($formattedDate);
        try{
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
            // Log::channel('cronJobLog')->info('s ' . 'bot-call requested' . json_encode($data));

            $validate = Validator::make($data, $validations);
            if ($validate->fails()) {
                return response()->json(['status' => 0, 'errors' => $validate->errors()], 422);
            }
            $findShipmentId = Shipment::where('tracking_number', $request->input('tracking_number'))->first();
            // Log::channel('cronJobLog')->info('s ' . 'Request All' . json_encode($request->all()));
            // Log::channel('cronJobLog')->info('s ' . 'Api Does Not exists' . json_encode(ApiZongLog::where(['shipment_id' => $findShipmentId, 'call_date_time' => $request->start_date])->doesntExist()));
            // Log::channel('cronJobLog')->info('s ' . 'OPS LOG' . ApiZongLog::where(['shipment_id' => $findShipmentId, 'call_date_time' => $request->start_date])->doesntExist());
            if (RvShipmentAgent::where('agent_id', $request->admin_id)->doesntExist()) {
                $new = new RvShipmentAgent();
                $new->agent_id = $request->admin_id;
                $new->total_shipments = 0;
                $new->actual_productivity = 0;
                $new->save();
            }
            if(ApiZongLog::where(['shipment_id' => $findShipmentId->id, 'call_date_time' => $request->start_date])->doesntExist()){
                if (in_array($findShipmentId->shipper_status_id, [12, 52, 65, 66]) && RvShipmentTicket::where('shipment_id', $findShipmentId->id)->whereNull('deleted_at')->where('is_bot', 1)->exists()) {
                    // RvShipmentTicket::where('shipment_id', $findShipmentId->id)->update(['in_progress' => 1]);
                    
                    $noAnswer = [
                        'ANSWER' => 34,
                        'BUSY' => 32,
                        'CONGESTION' => 39,
                        'NOANSWER' => 33,
                        'InvalidNumber' => 28,
                    ];            
                    $array = [
                        0 => [
                            'status_id' => 6,
                            'call_finding_id' => $noAnswer[$request->call_status],
                            'call_status_type' => ($request->call_status == 'ANSWER' ? 'Connected' :  'Not Connected'),
                        ], // unresponsive
                        1 => [
                            'status_id' => 2,
                            'call_finding_id' => 35,
                            'call_status_type' => 'Connected',
                        ], // reattempt
                        2 => [
                            'status_id' => 1,
                            'call_finding_id' => 36,
                            'call_status_type' => 'Connected',
                        ], // retrurn
                        3 => [
                            'status_id' => 3,
                            'call_finding_id' => 37,
                            'call_status_type' => 'Connected',
                        ], // manual
                    ];
                    DB::table('api_zong_logs')->insert([
                        'name' => $request->channel_name ??  'zong',
                        'api_request' => json_encode($request->all()), // log the request data
                        'status_code' => 200,
                        'shipment_id' => $findShipmentId->id,
                        'call_date_time' => $request->start_date,
                        'created_at' => now(),
                    ]);
                    $shipment_assign_agent = RvShipmentAssignAgent::where('shipment_id', $findShipmentId->id)->whereIn('rv_state_id', [1, 3])->latest()->first();
                    $request->merge(['agent_id' => $request->admin_id, 'shipment_id' => $findShipmentId->id, 'rv_assign_agent_status_id' => null, 'rv_assign_agent_sub_status_id' => ($request->call_finding ? $request->call_finding : $array[$request->input]['call_finding_id']), 'rv_assign_agent_status_id' => $array[$request->input]['status_id'], 'call_count' => 1, 'call_to_id' => 1, 'call_status' => $array[$request->input]['call_status_type'],'end_date' => $request->end_date]);
                    // RvCronLog::create([
                    //     'message' => json_encode($request->all()),
                    // ]);
                    // if($shipment_assign_agent){
                    if ($request->input > 0) {
                        $status = new RvAgentCallHistory();
                        $status->shipment_id = $request->shipment_id;
                        $status->rv_shipment_assign_agent_id = $shipment_assign_agent->id;
                        $status->call_finding_id = $request->rv_assign_agent_sub_status_id; //call finding reasons
                        $status->call_to_id = 1; //Shipper or Consignee
                        $status->remarks = $request->remarks;
                        $status->updated_type_id = Auth::guard('agent')->check() ? 2 : 1;
                        $status->updated_by_id = $request->admin_id;
                        $status->call_status = $request->call_status;
                        $status->updated_at = $request->end_date;
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
    
                            if ($request->input == 0) {
                                unset($data['message']['rv_agent_call_history_record_id']);
    
                                // if (RvShipmentAssignAgent::join('rv_shipment_tickets as rst', 'rst.shipment_id', 'rv_shipment_assign_agents.shipment_id')->where('rv_shipment_assign_agents.unresponsive_count', 1)->where('rv_shipment_assign_agents.shipment_id', $findShipmentId->id)->exists()) {
    
                                //     $globalSettingValue = GlobalSettings::where('type', 'second_bot_call')->select('setting_value')->first();
                                //     $job = (new BotCallDispatchSecod($findShipmentId->id))->delay(60 * $globalSettingValue['setting_value'])->onConnection('jobs_2');
                                //     // Log::channel('cronJobLog')->info('s ' . 'bot-call requested (unreponsive count 1)' . $findShipmentId->id);
                                //     $this->dispatch($job);
                                // } elseif (RvShipmentAssignAgent::join('rv_shipment_tickets as rst', 'rst.shipment_id', 'rv_shipment_assign_agents.shipment_id')->where('rv_shipment_assign_agents.unresponsive_count', 2)->where('rv_shipment_assign_agents.shipment_id', $findShipmentId->id)->exists()) {
    
                                //     $globalSettingValue = GlobalSettings::where('type', 'third_bot_call')->select('setting_value')->first();
                                //     $job = (new BotCallDispatch($findShipmentId->id))->delay(60 * $globalSettingValue['setting_value']);
                                //     // Log::channel('cronJobLog')->info('s ' . 'bot-call requested (unreponsive count 2)' . $findShipmentId->id);
                                //     $this->dispatch($job)->onConnection('jobs_2');
                                // }
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
                } else {
                    
                    $request->merge(['agent_id' => $request->admin_id, 'shipment_id' => $findShipmentId->id, 'rv_assign_agent_status_id' => null, 'rv_assign_agent_sub_status_id' => 38, 'rv_assign_agent_status_id' => 9, 'call_count' => 1, 'call_to_id' => 1, 'call_status' => ($request->call_status == 'ANSWER' ? 'Connected' :  'Not Connected'),'end_date' => $request->end_date]);
                    $this->shipmentDifferentStatus($findShipmentId->id,$request);
                    RvShipmentTicket::where('shipment_id', $findShipmentId->id)->delete();
                    
                    DB::table('api_zong_logs')->insert([
                        'name' => $request->channel_name ?? 'zong',
                        'api_request' => json_encode($request->all()), // log the request data
                        'status_code' => 200,
                        'shipment_id' => Shipment::where('tracking_number',$request->input('tracking_number'))->first()->id,
                        'error' => json_encode('Shipment is in different status, Cannot mark it as Another Status!'),
                        'call_date_time' => $request->start_date,
                        'created_at' => now(),
                    ]);
                    $data = [
                        'status' => 0,
                        'message' => 'Shipment is in different status, Cannot mark it as Another Status!'
                    ];
                }
                return response()->json(['status' => 1, 'message' => $data]);
            }else{
                return response()->json(['status' => 1, 'message' => 'Record Already exists.']);
            }

        } catch (\Throwable $th) {
            DB::table('api_zong_logs')->insert([
                'name' => $request->channel_name ??  'zong',
                'api_request' => json_encode($request->all()), // log the request data
                'error' => json_encode($th->getMessage()), // log the request data
                'status_code' => 400,
                'shipment_id' => Shipment::where('tracking_number', $request->input('tracking_number'))->first()->id,
                'call_date_time' => $request->start_date,
                'created_at' => now(),
            ]);
            // Log::channel('cronJobLog')->info('s ' . 'OPS LOG' . $th->getMessage());
            return response()->json(['status' => 0, 'message' => $th->getMessage()]);

        }
        
    }
}
