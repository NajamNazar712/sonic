<?php

namespace App\Http\Models\CRM\Escalation;

use Illuminate\Database\Eloquent\Model;

class CrmEscalationTaggingLevelRole extends Model
{
    public function role() {
        return $this->belongsTo('App\Http\Models\Admin\AdminRole', 'role_id', 'id');
    }
}
