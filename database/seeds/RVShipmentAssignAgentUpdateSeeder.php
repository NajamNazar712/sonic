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
        $rvshipment = RvShipmentAssignAgent::join('users as u','u.id','rv_shipment_assign_agents.updated_by_id')->where('updated_type_id',1)->pluck('rv_shipment_assign_agents.id');
        $rvShipmentAssignAgentUpdate = RvShipmentAssignAgent::whereIN('id',$rvshipment)->update(['updated_type_id'=>3]);
        $rvShipmentdetailUpdate = RvShipmentAssignAgentDetails::whereIN('rv_shipment_assign_agent_id',$rvshipment)->update(['updated_type_id'=>3]);
    }
}
