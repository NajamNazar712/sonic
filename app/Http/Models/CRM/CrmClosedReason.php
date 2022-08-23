<?php

namespace App\Http\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class CrmClosedReason extends Model
{
    public function crm_request() {
        return $this->belongsTo('App\Http\Models\CRM\CrmRequest');
    }
}
