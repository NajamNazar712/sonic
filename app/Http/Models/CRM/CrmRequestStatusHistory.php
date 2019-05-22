<?php

namespace App\Http\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class CrmRequestStatusHistory extends Model
{
    protected $fillable = [
        'crm_request_id', 'status_id', 'agent_id'
    ];
    public function agent() {
        return $this->belongsTo('App\Http\Models\Admin\Admin','agent_id','id');
    }
    public function status() {
        return $this->belongsTo('App\Http\Models\CRM\CrmRequestStatus','status_id','id');
    }
}
