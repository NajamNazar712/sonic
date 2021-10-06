<?php

namespace App\Http\Models\CRM\Escalation;

use Illuminate\Database\Eloquent\Model;

class CrmEscalation extends Model
{
    public function nature() {
        return $this->belongsTo('App\Http\Models\CRM\CrmRequestCaseNature','case_nature','id');
    }
    public function nature_type() {
        return $this->belongsTo('App\Http\Models\CRM\CrmRequestCaseNatureType','case_nature_type','id');
    }
    public function shipment_statuses(){
        return $this->hasMany('App\Http\Models\CRM\Escalation\CrmEscalationShipmentStatus', 'escalation_id', 'id');
    }
}
