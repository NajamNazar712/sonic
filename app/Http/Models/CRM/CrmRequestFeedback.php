<?php

namespace App\Http\Models\CRM;

use Illuminate\Database\Eloquent\Model;

class CrmRequestFeedback extends Model
{
    public function crm_request() {
        return $this->belongsTo('App\Http\Models\CRM\CrmRequest');
    }
}
