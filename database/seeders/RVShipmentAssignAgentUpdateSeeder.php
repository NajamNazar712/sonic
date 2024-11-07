<?php

use App\Http\Models\RvShipmentAssignAgent;
use App\Http\Models\RvShipmentAssignAgentDetails;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class RVShipmentAssignAgentUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $rvshipment = RvShipmentAssignAgent::join('users as u','u.id','rv_shipment_assign_agents.updated_by_id')->get(['rv_shipment_assign_agents.id','rv_shipment_assign_agents.updated_type_id']);
        
        foreach($rvshipment as $updatedbyid)
        {
            if($updatedbyid['updated_type_id'] === 1)
            {
                $rvShipmentAssignAgentUpdate = RvShipmentAssignAgent::where('id',$updatedbyid['id'])->update(['updated_type_id'=>3]);
                $rvShipmentdetailUpdate = RvShipmentAssignAgentDetails::where('rv_shipment_assign_agent_id',$updatedbyid['id'])->update(['updated_type_id'=>3]);
            }
        }
    }
}
