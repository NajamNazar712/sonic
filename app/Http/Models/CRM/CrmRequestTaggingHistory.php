<?php

namespace App\Http\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class CrmRequestTaggingHistory extends Model
{
    protected $fillable = [
        'crm_request_id','crm_request_tagging_type_id','tagged_id','agent_id'
    ];
    public function tagging() {
        return $this->belongsTo('App\Http\Models\CRM\CrmRequestTaggingTypes','crm_request_tagging_type_id','id');
    }
    public function agent() {
        return $this->belongsTo('App\Http\Models\Admin\Admin','agent_id','id');
    }
    public function department() {
        return $this->belongsTo('App\Http\Models\Admin\AdminDepartment','tagged_id','id');
    }
    public function user() {
        return $this->belongsTo('App\Http\Models\Admin\Admin','tagged_id','id');
    }
}
