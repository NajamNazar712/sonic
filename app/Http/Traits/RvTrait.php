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
            'cosignee_cities'=> $consignee_cities,
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
            return ['satus' => 0, 'error' => $th->getMessage()];
            //throw $th;
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

}
