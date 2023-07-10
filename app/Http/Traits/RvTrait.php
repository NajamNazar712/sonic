<?php

namespace App\Http\Traits;

use App\Http\Models\RestrictedCityIntercept;
use App\Http\Models\RvShipmentAssignAgent;
use App\Http\Models\RvShipmentAssignAgentDetails;
use App\Http\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait RvTrait {


    protected function getShipmentConsigneeCities($shipment_id) {
        if (!empty($shipment_id)) {
            $shipment = Shipment::find($shipment_id);
            if ($shipment) {
                $consignee_cities = Shipment::leftjoin('city_deliveries as cd', 'cd.booking_type_id', '=', 'shipments.booking_type_id')
                    ->leftjoin('cities as c', 'c.id', '=', 'cd.city_id')
                    ->select('c.id as id', 'c.name as name')
                    ->where('shipments.id', $shipment)
                    ->where('c.status', 1)
                    ->whereNotNull('c.zone_id');
                if ($shipment->shipping_mode_id == 2) {
                    $restricted_cities = RestrictedCityIntercept::pluck('city_id')->toArray();
                    $consignee_cities->where('cd.shipping_mode_id', 2)
                        ->whereNotIn('c.id', $restricted_cities);
                }
                $consignee_cities = $consignee_cities->orderBy('c.name')
                    ->groupBy('c.name')
                    ->get();
            }
        }

        return [
            'consignee_cities'=> $consignee_cities,
            'shipment'=> $shipment,
        ];
    }


    protected function newRvShipmentAssign($data)
    {
        try {
            RvShipmentAssignAgent::updateOrCreate(
                [
                    'shipment_id' => $data['shipment_id'],
                ],
                [
                    'agent_id' => Auth::id(),
                    'rv_state_id' => $data['rv_state_id'] ?? 1,
                    'rv_assign_agent_status_id' => $data['rv_assign_agent_status_id'] ?? null,
                    'rv_assign_agent_sub_status_id' => $data['rv_assign_agent_sub_status_id'] ?? null,
                ]
                
            );
                return true;
        } catch (\Throwable $th) {
            return ['status' => 0, 'error' => $th->getMessage()];
        }


    }

    protected function makeRvShipmentAssignAgentDetails($shipment_assign_agent, $request) {
        $rv_shipment_assign_agent_details  = new RvShipmentAssignAgentDetails();
        $rv_shipment_assign_agent_details->rv_shipment_assign_agent_id = $shipment_assign_agent->id;
        $rv_shipment_assign_agent_details->agent_id = Auth::id();
        $rv_shipment_assign_agent_details->shipment_id = $request->shipment_id;
        $rv_shipment_assign_agent_details->rv_assign_agent_status_id = $shipment_assign_agent->rv_assign_agent_status_id;
        $rv_shipment_assign_agent_details->rv_assign_agent_sub_status_id = $shipment_assign_agent->rv_assign_agent_sub_status_id;
        $rv_shipment_assign_agent_details->rv_state_id = $shipment_assign_agent->rv_state_id;
        $rv_shipment_assign_agent_details->updated_type_id = $shipment_assign_agent->updated_type_id;
        $rv_shipment_assign_agent_details->updated_by_id = $shipment_assign_agent->updated_by_id;
        $rv_shipment_assign_agent_details->is_fake_status = $request->is_fake_status;
        $rv_shipment_assign_agent_details->rv_fake_status_id = $request->rv_fake_status_id;
        $rv_shipment_assign_agent_details->remarks = $request->remarks;
        $rv_shipment_assign_agent_details->call_to_id  = $request->call_to_id;
        $rv_shipment_assign_agent_details->save();

        return true;
    }

    private function shipment_assign_agent_table_columns($request, $assign_agent) {
        return [
            'rv_assign_agent_status_id' => $request->rv_assign_agent_status_id,
            'rv_assign_agent_sub_status_id' => $request->rv_assign_agent_sub_status_id,
            'rv_fake_status_id' => $request->fake_status,
            'remarks' => $request->shipment_remarks,
            'rv_state_id' => $request->is_fake_status,
            'call_to_id' => $request->call_to_id,
            'updated_by_id' => Auth::id(),
            'is_fake_status' => $request->fake_status,
            'rv_shipment_agent_id' => $assign_agent->id,
        ];
    }
    
    protected function updateShipmentAssignAgent($request, $assign_agent, $admin_agent, $shipment_assign_agent){
        $shipment_assign_agent_table_columns = $this->shipment_assign_agent_table_columns($request, $assign_agent);

        if ($admin_agent->employee->staff_category_id == 3) {
            $assign_agent->increment('total_shipments');
            $assign_agent->increment('actual_productivity');
            $shipment_assign_agent_table_columns['updated_type_id'] = 2; // agent type
        } 

        else {
            $assign_agent->increment('already_updated');
            $shipment_assign_agent_table_columns['updated_type_id'] = 1; // admin type
        }

        $shipment_assign_agent->update($shipment_assign_agent_table_columns);

        return true;
    }


    //     public function update_rv_assigned_shipment_status($rv_assigned_shipment, $status, $column_name){
        
    //     //Assuring if agent is updating the status update rows in rcp_assigned_agent
    //     $rv_assigned_shipment = $rv_assigned_shipment ->latest()->first();
    //     if($rv_assigned_shipment->admin_id == Auth::id()){

    //         $rv_assigned_shipment->shipment_status = $status;
    //         $rv_assigned_shipment->admin_id = Auth::id();
    //         $rv_assigned_shipment->save();
            

    //         //updating return row of agent 
    //         $rcp_assigned_agent = RcpAssignedAgent::where('id',$rv_assigned_shipment->rcp_assigned_agent_id)->first();
    //         $rcp_assigned_agent->increment($column_name);
    //         $rcp_assigned_agent->decrement('pending_shipments');
    //         $rcp_assigned_agent->increment('actual_productivity');
    //         $rcp_assigned_agent->admin_id = Auth::id();
    //         $rcp_assigned_agent->save();

    //         //creating log 
    //         $this->makeRvShipmentAssignAgentDetails($rv_assigned_shipment, $request);
    //         }
            
    //     //If admin is updating the status update rcp_assigned_shipment & log
    //         else{
    //         $rv_assigned_shipment = $rv_assigned_shipment ->latest()->first();
    //         $rv_assigned_shipment->shipment_status = $status;
    //         $rv_assigned_shipment->admin_id = Auth::id();
    //         $rv_assigned_shipment->save();

    //         //updating already_updated & pending of agent if shipment is updated by admin 
    //         $rcp_assigned_agent = RcpAssignedAgent::where('id',$rv_assigned_shipment->rcp_assigned_agent_id)->first();
    //         $rcp_assigned_agent->increment('already_updated');
    //         $rcp_assigned_agent->decrement('pending_shipments');
    //         $rcp_assigned_agent->save();


    //         $return_assign_log = new RcpAssignedShipmentLog();
    //         $return_assign_log->rcp_assigned_shipment_id = $rv_assigned_shipment->id;
    //         $return_assign_log->shipment_id = $rv_assigned_shipment->shipment_id;
    //         $return_assign_log->status = $status;
    //         $return_assign_log->admin_id = Auth::id();
    //         $return_assign_log->save();
    //         }
    // }

}
