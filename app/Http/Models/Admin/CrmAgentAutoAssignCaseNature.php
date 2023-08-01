<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class CrmAgentAutoAssignCaseNature extends Model
{
    public function case_natures() {
        return $this->belongsTo('App\Http\Models\CRM\CrmRequestCaseNature', 'case_nature_id', 'id');
    }
}
