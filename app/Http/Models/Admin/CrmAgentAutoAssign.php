<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class CrmAgentAutoAssign extends Model
{
    public function hubs() {
        return $this->hasMany('App\Http\Models\Admin\CrmAgentAutoAssignHub', 'agent_id', 'agent_id');
    }
    public function zones() {
        return $this->hasMany('App\Http\Models\Admin\CrmAgentAutoAssignZone', 'agent_id', 'agent_id');
    }
    public function case_natures() {
        return $this->hasMany('App\Http\Models\Admin\CrmAgentAutoAssignCaseNature', 'agent_id', 'agent_id');
    }
    public function case_nature_types() {
        return $this->hasMany('App\Http\Models\Admin\CrmAgentAutoAssignCnType', 'agent_id', 'agent_id');
    }
    public function business_types() {
        return $this->hasMany('App\Http\Models\Admin\CrmAgentAutoAssignBusSeg', 'agent_id', 'agent_id');
    }
    public function sub_business_types() {
        return $this->hasMany('App\Http\Models\Admin\CrmAgentAutoAssignSubSegment', 'agent_id', 'agent_id');
    }
    public function shipper_keys() {
        return $this->hasMany('App\Http\Models\Admin\CrmAgentAutoAssignShipper', 'agent_id', 'agent_id');
    }
    public function shipper_non_keys() {
        return $this->hasMany('App\Http\Models\Admin\CrmAgentAutoAssignSNKey', 'agent_id', 'agent_id');
    }
    public function shipment_statuses() {
        return $this->hasMany('App\Http\Models\Admin\CrmAgentAutoAssignShipStatus', 'agent_id', 'agent_id');
    }
}
