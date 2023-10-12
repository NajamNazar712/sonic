<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RvShipmentAssignAgent extends Model
{
   protected $fillable = [
      'agent_id', 'shipment_id', 'rv_assign_agent_status_id', 'rv_assign_agent_sub_status_id',
      'rv_state_id', 'is_fake_status', 'rv_fake_status_id', 'remarks', 'call_to_id',
      'assigned_by', 'updated_type_id', 'updated_by_id', 'rv_shipment_agent_id', 'shipments_journey_id', 'last_shipments_journey_id', 'unresponsive_count', 'unresponsive_attempt_time'
   ];

   public function rv_sub_status()
   {
      return $this->belongsTo('App\RvAssignAgentSubStatus', 'rv_assign_agent_sub_status_id', 'id');
   }
   
   public function max_rv_shipment_assign_agent_detail()
   {
      return $this->hasOne('App\Http\Models\RvShipmentAssignAgentDetails', 'rv_shipment_assign_agent_id')->orderBy('id', 'desc');
   }

   public function shipment()
   {
      return $this->belongsTo('App\Http\Models\Shipment', 'shipment_id', 'id');
   }

   public function admin()
   {
      return $this->belongsTo('App\Http\Models\Admin', 'updated_by_id', 'id');
   }
}
