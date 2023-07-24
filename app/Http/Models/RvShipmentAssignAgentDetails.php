<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RvShipmentAssignAgentDetails extends Model
{
    protected $fillable = ['rv_shipment_assign_agent_id','agent_id', 'shipment_id', 'shipments_journey_id', 'last_shipments_journey_id', 'rv_assign_agent_status_id', 'rv_assign_agent_sub_status_id', 
   'rv_state_id', 'updated_type_id', 'updated_by_id', 'is_fake_status','rv_fake_status_id', 'remarks', 'call_to_id'];
}
