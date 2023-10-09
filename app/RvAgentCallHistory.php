<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RvAgentCallHistory extends Model
{
   //  public function rv_call_finding()
   //  {
   //     return $this->belongsTo('App\Http\Models\Admin\SubStatusCallFinding', 'call_finding_id', 'id');
   //  }

   public function rv_call_finding()
    {
       return $this->belongsTo('App\RvAssignAgentSubStatus', 'call_finding_id');
    }

    
    public function user()
    {
       return $this->belongsTo('App\Http\Models\RvShipmentAssignAgent', 'rv_shipment_assign_agent_id', 'id');
    }
    
    
    public function shipment()
    {
       return $this->belongsTo('App\Http\Models\Shipment', 'shipment_id', 'id');
    }
}
