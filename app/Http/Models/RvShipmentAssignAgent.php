<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RvShipmentAssignAgent extends Model
{
   protected $fillable = ['agent_id', 'shipment_id','rv_assign_agent_status_id', 'rv_assign_agent_sub_status_id', 'rv_state_id','is_fake_status','rv_fake_status_id', 'remarks', 'call_to', 'unassigned_datetime'];
}
