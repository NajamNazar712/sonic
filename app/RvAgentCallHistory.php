<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RvAgentCallHistory extends Model
{
    public function rv_call_finding()
    {
       return $this->belongsTo('App\Http\Models\Admin\SubStatusCallFinding', 'call_finding_id', 'id');
    }

    
    public function user()
    {
       return $this->belongsTo('App\Http\Models\RvShipmentAssignAgent', 'rv_shipment_assign_agent_id', 'id');
    }
}
