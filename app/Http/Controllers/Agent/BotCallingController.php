<?php

namespace App\Http\Controllers\Agent;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Admin\Admin;
use App\Http\Models\RvShipmentAssignAgent;
use App\Http\Models\Shipment;
use App\Http\Models\ShipmentsJourney;
use App\Http\Models\Shipper\User;
use App\Http\Traits\RvTrait;
use App\RvAgentCallHistory;
use App\RvAssignAgentSubStatus;
use App\RvShipmentAgent;
use App\RvShipmentTicket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BotCallingController extends Controller
{
    use RvTrait;

    public function bot_get_ticket_details(Request $request)
    {
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

    // public function submit_ticket(Request $request)
    // {
    //     $validations = [
    //         'rv_assign_agent_status_id' => 'required',
    //         'rv_assign_agent_sub_status_id' => 'required_unless:rv_assign_agent_status_id, 2, 3, 8',
    //         // 'remarks' => Rule::requiredIf(function () use ($request) {
    //         //     return $request->rv_assign_agent_status_id == 6 && $request->rv_assign_agent_sub_status_id == 1 || $request->rv_assign_agent_status_id == 5;
    //         // }), //if unresponsive and other is selected remark is required
    //     ];

    //     $data = [
    //         'rv_assign_agent_status_id' => $request->input('rv_assign_agent_status_id'),
    //         'rv_assign_agent_sub_status_id' => $request->input('rv_assign_agent_sub_status_id'),
    //         // 'is_fake_status' => $request->input('is_fake_status'),
    //         // 'rv_fake_status_id' => $request->input('rv_fake_status_id') ?? null,
    //         // 'remarks' => $request->input('remarks') ?? null,
    //     ];

    //     $validate = Validator::make($data, $validations);

    //     if ($validate->fails()) {
    //         return response()->json(['status' => 1, 'errors' => $validate->errors()]);
    //     } 
    //     else 
    //     {
    //         $shipment_assign_agent = RvShipmentAssignAgent::where('shipment_id', $request->shipment_id)->where('rv_state_id', 1)->latest()->first();
    //         if($shipment_assign_agent){
    //             $assign_agent = RvShipmentAgent::where('agent_id', $shipment_assign_agent->agent_id)->whereDate('created_at', date('Y-m-d'))->first();
    //             $admin_agent = Admin::where('id', Auth::id())->first();       
    //             $shipments_journey = ShipmentsJourney::where('shipment_id', $request->shipment_id)->latest()->first();
    //             // $employee = Employee::where('phone_number', $request->phone_number)->first();
    //             // $employee_shift = EmployeeShift::where('id', $employee->shift_id)->first();
    
    //             // Check Employee Shift Time
    //                 try{
    //                     DB::beginTransaction();
    //                 //if agent already exists on same date update row
    //                     if ($request->is_fake_status > 0) {
    //                         $this->fakeStatusMarkedDeliveries($request);
    //                     }
    //                     if ($assign_agent) 
    //                     {
    //                         $update_shipment_status = $this->update_shipment_status($request); //updating status of shipment
    //                         if ($update_shipment_status['status'] == 0) {
    //                             DB::rollBack();
    //                             return response()->json(['status' => 4, 'error' => $update_shipment_status['error']]);
    //                         } 
    //                         else{
    //                             $rv_agent_call_history_record_id = $update_shipment_status['rv_agent_call_history_record_id'] ?? null;
    //                             $shipment_assign_agents = RvShipmentAssignAgent::where('shipment_id', $request->shipment_id)->where('rv_state_id', 2)->where('rv_assign_agent_status_id', 8)->latest()->first();
    //                             if($shipment_assign_agents){
    //                                 $shipment_assign_agent = $shipment_assign_agents;
    //                             }

    //                             else{
    //                                 // this function is updating rv_shipment_assign_agents table columns like increment total_shipments, actual_productivity, already_updated, updated_type_id, rv_state_id
    //                                 $add_shipment_agent = $this->update_shipment_assign_agent($request, $assign_agent, $admin_agent, $shipment_assign_agent);
    //                                 if($add_shipment_agent != true){
    //                                     DB::rollBack();
    //                                     return response()->json(['status' => 3, 'errors' => 'Agent Not Updated']);
    //                                 } 
    //                                 else {
    //                                     // $shipment_assign_agents = RvShipmentAssignAgent::where('shipment_id', $request->shipment_id)->where('rv_state_id', 2)->latest()->first();
    //                                     $shipment_assign_agent = RvShipmentAssignAgent::where('shipment_id', $request->shipment_id)->latest()->first();
    //                                     // //adding logs in rv_shipment_assign_agent_details table
    //                                     $rv_shipment_assign_agent_details = $this->rv_shipment_assign_agent_details($request, $shipment_assign_agent, $shipments_journey, $rv_agent_call_history_record_id);
    //                                     if($rv_shipment_assign_agent_details != true){
    //                                         DB::rollBack();
    //                                         return response()->json(['status' => 3, 'errors' => 'Shipment Details not updated']);
    //                                     } 
    //                                     DB::commit();
    //                                     return response()->json(['status' => 0, 'success' => 'Shipment Status Updated!']);
    //                                 }
    //                             }
    //                         }
    //                     }

    //                     //when there is no row of agent in rv_shipment_agents table i.e update status on different day 
    //                     else {
    //                         $update_shipment_status = $this->update_shipment_status($request); //updating status of shipment
    //                         if ($update_shipment_status['status'] == 0) {
    //                             DB::rollBack();
    //                             return response()->json(['status' => 4, 'error' => $update_shipment_status['error']]);
    //                         } 
    //                         else {
    //                             $shipment_assign_agents = RvShipmentAssignAgent::where('shipment_id', $request->shipment_id)->where('rv_state_id', 2)->where('rv_assign_agent_status_id', 8)->latest()->first();
    //                             if($shipment_assign_agents){
    //                                 $shipment_assign_agent = $shipment_assign_agents;
    //                             }
    //                             else {
    //                                 $shipment_assign_agent = RvShipmentAssignAgent::where('shipment_id', $request->shipment_id)->where('rv_state_id', 1)->latest()->first();
    //                                 // this function is updating rv_shipment_assign_agents table columns like increment total_shipments, actual_productivity, already_updated, updated_type_id, rv_state_id
    //                                 $add_shipment_agent = $this->add_shipment_agent($request, $shipment_assign_agent);
    //                                 if($add_shipment_agent != true){
    //                                     DB::rollBack();
    //                                     return response()->json(['status' => 3, 'errors' => 'Agent Not Updated']);
    //                                 } else {
    //                                     $rv_shipment_assign_agent_details = $this->rv_shipment_assign_agent_details($request, $shipment_assign_agent, $shipments_journey);
    //                                     if($rv_shipment_assign_agent_details != true){
    //                                         DB::rollBack();
    //                                         return response()->json(['status' => 3, 'errors' => 'Shipment Details not updated']);
    //                                     } 
    //                                     else{
    //                                         DB::commit();
    //                                         return response()->json(['status' => 0, 'success' => 'Shipment Status Updated!']);
    //                                     }
    //                                 }
    //                             }
    //                         }
    //                     }
    //                 }
    //                 catch(\Throwable $th)
    //                 {
    //                     DB::rollBack();
    //                     return response()->json(['status' => 3, 'errors' => 'Something Went Wrong', 'info'=> $th->getMessage()]);
    //                 }
    //         }
    //         else{
    //             return response()->json(['status' => 3, 'errors' => 'This Shipment is Unassigned']);
    //         }
    //     }

    // }

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
        $findShipmentId = Shipment::where('tracking_number', $request->input('tracking_number'))->first();
        RvShipmentTicket::where('shipment_id', $findShipmentId->id)->update(['in_progress' => 1]);
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
        $request->request->add(['agent_id' => $request->admin_id, 'shipment_id' => $findShipmentId->id, 'rv_assign_agent_status_id' => $request->call_status, 'remarks' => 'bot calladd the ' . $array[$request->input]['remarks'], 'rv_assign_agent_status_id' => $array[$request->input]['status_id'], 'call_count' => 1]);
        
        $shipment_assign_agent = RvShipmentAssignAgent::where('shipment_id', $findShipmentId->id)->where('rv_state_id', 1)->latest()->first();
        // if($shipment_assign_agent){
        if($request->input > 0){
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
        $assigned_agent = RvShipmentAgent::where('agent_id',$request->admin_id)->first();
        $admin_agent = Admin::where('id',$request->admin_id)->first();
        $this->update_shipment_assign_agent($request, $assigned_agent, $admin_agent, $shipment_assign_agent);
        $shipments_journey = ShipmentsJourney::where('shipment_id', $request->shipment_id)->latest()->first();
        if($request->input != 3){ // not for the further assistance
            $data = $this->update_shipment_status($request,1);
            if($data['status'] == 1){ //data add successfully
                $shipment_assign_agent = RvShipmentAssignAgent::where('shipment_id', $findShipmentId->id)->latest()->first();

                $this->rv_shipment_assign_agent_details($request, $shipment_assign_agent, $shipments_journey, $data['rv_agent_call_history_record_id'] ?? $status->id);          
            }
            unset($data['message']['rv_agent_call_history_record_id']);
        }else{
            $this->rv_shipment_assign_agent_details($request, $shipment_assign_agent, $shipments_journey, $status->id);

            RvShipmentTicket::where('shipment_id', $findShipmentId->id)->update(['in_progress' => 0,'is_bot'=>0]);
            $data = [
                'status' => 1,
                'message' => 'Shipment is assign to manual agent'
            ];
        }
        
        return response()->json(['status' => 1,'message' => $data]);
    }
}
