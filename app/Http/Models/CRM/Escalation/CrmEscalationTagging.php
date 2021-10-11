<?php

namespace App\Http\Models\CRM\Escalation;

use Illuminate\Database\Eloquent\Model;

class CrmEscalationTagging extends Model
{
    public function nature() {
        return $this->belongsTo('App\Http\Models\CRM\CrmRequestCaseNature','case_nature','id');
    }
    public function nature_type() {
        return $this->belongsTo('App\Http\Models\CRM\CrmRequestCaseNatureType','case_nature_type','id');
    }
    public function shipment_statuses(){
        return $this->hasMany('App\Http\Models\CRM\Escalation\CrmEscalationTaggingShipmentStatus', 'escalation_tagging_id', 'id');
    }
    public function hubs(){
        return $this->hasMany('App\Http\Models\CRM\Escalation\CrmEscalationTaggingHub', 'escalation_tagging_id', 'id');
    }
    public function levels(){
        return $this->hasMany('App\Http\Models\CRM\Escalation\CrmEscalationTaggingLevel', 'escalation_tagging_id', 'id');
    }
}
