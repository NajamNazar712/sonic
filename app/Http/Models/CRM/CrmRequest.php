<?php

namespace App\Http\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class CrmRequest extends Model
{
    public function nature() {
        return $this->belongsTo('App\Http\Models\CRM\CrmRequestCaseNature','case_nature_id','id');
    }
    public function nature_type() {
        return $this->belongsTo('App\Http\Models\CRM\CrmRequestCaseNatureType','case_nature_type_id','id');
    }
    public function channel() {
        return $this->belongsTo('App\Http\Models\CRM\CrmRequestChannel','channel_id','id');
    }
    public function request_status() {
        return $this->belongsTo('App\Http\Models\CRM\CrmRequestStatus','status_id','id');
    }
    public function agent() {
        return $this->belongsTo('App\Http\Models\Admin\Admin','agent_id','id');
    }
    public function launched_by_admin() {
        return $this->belongsTo('App\Http\Models\Admin\Admin','launched_by_id','id');
    }
    public function shipment() {
        return $this->belongsTo('App\Http\Models\Shipment','shipment_id','id');
    }

}
