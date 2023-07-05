<?php

namespace App\Http\Traits;

use App\Http\Models\RestrictedCityIntercept;
use App\Http\Models\RvShipmentAssignAgent;
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

}
