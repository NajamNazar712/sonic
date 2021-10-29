<?php

namespace App\Http\Models\CRM\Escalation;

use Illuminate\Database\Eloquent\Model;

class CrmEscalationTaggingHub extends Model
{
    public function hub() {
        return $this->belongsTo('App\Http\Models\City', 'hub_id', 'id');
    }
}
