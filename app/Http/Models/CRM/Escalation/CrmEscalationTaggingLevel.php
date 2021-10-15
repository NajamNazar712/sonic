<?php

namespace App\Http\Models\CRM\Escalation;

use Illuminate\Database\Eloquent\Model;

class CrmEscalationTaggingLevel extends Model
{
    public function level() {
        return $this->belongsTo('App\Http\Models\CRM\Escalation\CrmEscalationLevel', 'level_id', 'id');
    }
    public function roles() {
        return $this->hasMany('App\Http\Models\CRM\Escalation\CrmEscalationTaggingLevelRole', 'tagging_level_id', 'id');
    }
    public function emails(){
        return $this->hasMany('App\Http\Models\CRM\Escalation\CrmEscalationTaggingLevelEmail', 'tagging_level_id', 'id');
    }
}
