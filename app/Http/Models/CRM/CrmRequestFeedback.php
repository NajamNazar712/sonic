<?php

namespace App\Http\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class CrmRequestFeedback extends Model
{

    protected $fillable = ['crm_request_id','rating_id'];
    
    public function crm_request() {
        return $this->belongsTo('App\Http\Models\CRM\CrmRequest');
    }
}
