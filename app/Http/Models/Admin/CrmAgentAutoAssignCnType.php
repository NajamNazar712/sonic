<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class CrmAgentAutoAssignCnType extends Model
{
    public function case_nature_types() {
        return $this->belongsTo('App\Http\Models\CRM\CrmRequestCaseNatureType', 'case_nature_type_id', 'id');
    }
}
